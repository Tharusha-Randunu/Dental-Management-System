<?php
include __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userRole = strtolower($_SESSION['role'] ?? '');
$userNIC = $_SESSION['nic'] ?? '';

error_log("Session NIC: " . $userNIC); // Debug line (optional, can remove in production)

$notifications = [];

if ($userRole === 'admin' || $userRole === 'dentist') {
    $sql = "SELECT item_id, supplier_code, item_name, category, quantity, unit, reorder_level FROM inventory WHERE quantity <= reorder_level";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => 'stock_' . $row['item_id'] . '_' . $row['supplier_code'],
                'message' => "Stock low: {$row['item_name']} ({$row['category']}) - Only {$row['quantity']} {$row['unit']} left!",
                'read' => false,
                'item_id' => $row['item_id'],
                'supplier_code' => $row['supplier_code'],
                'type' => 'stock'
            ];
        }
    }
}

if ($userRole === 'admin' || $userRole === 'lab_technician') {
    $sqlLab = "SELECT tr.test_id, tr.patient_nic, tr.test_type_id, tr.request_date, tr.requested_by, tr.assigned_to, u.Fullname AS requested_by_name
               FROM test_requests tr
               LEFT JOIN users u ON tr.requested_by = u.NIC";

    if ($userRole === 'lab_technician' && !empty($userNIC)) {
        $sqlLab .= " WHERE tr.assigned_to = ?";
        $stmtLab = $conn->prepare($sqlLab);
        $stmtLab->bind_param("s", $userNIC);
    } else {
        $stmtLab = $conn->prepare($sqlLab);
    }

    if ($stmtLab && $stmtLab->execute()) {
        $resultLab = $stmtLab->get_result();
        while ($row = $resultLab->fetch_assoc()) {
            $notifications[] = [
                'id' => 'lab_' . $row['test_id'],
                'message' => "New lab test request for Patient NIC: {$row['patient_nic']} - Test Type ID: {$row['test_type_id']} (Requested by: {$row['requested_by_name']})",
                'read' => false,
                'test_id' => $row['test_id'],
                'type' => 'lab'
            ];
        }
    }
    if ($stmtLab) $stmtLab->close();
}

$newAlerts = false;
foreach ($notifications as $notif) {
    if (!$notif['read']) {
        $newAlerts = true;
        break;
    }
}
?>

<style>
  .notification-toggle-btn {
    position: fixed;
    top: 20px;
    right: 73px;
    font-size: 26px;
    color: #0d47a1;
    background-color: #fff;
    padding: 10px 12px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    z-index: 1000;
    transition: background-color 0.3s ease;
  }

  .notification-toggle-btn:hover {
    background-color: #bbdefb;
  }

  .notification-toggle-btn.glow {
    animation: glow-pulse 1.5s infinite alternate;
  }

  @keyframes glow-pulse {
    from {
      text-shadow: 0 0 5px #ff3d00, 0 0 10px #ff3d00;
      color: #ff3d00;
    }
    to {
      text-shadow: 0 0 20px #ff6e40, 0 0 30px #ff6e40;
      color: #ff6e40;
    }
  }

  .notification-dropdown {
    position: fixed;
    top: 80px;
    right: 80px;
    width: 320px;
    max-height: 350px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    overflow-y: auto;
    display: none;
    z-index: 950;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    border: none;
    padding: 12px 0;
  }

  .notification-dropdown.active {
    display: block;
  }

  .notification-item {
    padding: 12px 20px;
    border-bottom: 1px solid #eee;
    font-size: 15px;
    color: #444;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: background-color 0.25s ease;
    user-select: none;
    border-left: 4px solid transparent;
    border-radius: 0 8px 8px 0;
  }

  .notification-item:hover {
    background-color: #f0f4ff;
    border-left-color: #1976d2;
  }

  .notification-item.read {
    background-color: #f9f9f9;
    color: #888;
    font-weight: normal;
    border-left-color: transparent;
  }

  .notification-item.unread {
    background-color: #e8f0fe;
    font-weight: 600;
    border-left-color: #ff5722;
    color: #222;
  }

  .notification-item:last-child {
    border-bottom: none;
  }

  .notification-empty {
    padding: 28px;
    text-align: center;
    color: #999;
    font-style: italic;
    font-size: 14px;
  }

  @media (max-width: 576px) {
    .notification-toggle-btn {
      top: 20px;
      right: auto;
      left: 15px;
      font-size: 26px;
      padding: 10px 12px;
    }
    .notification-dropdown {
      top: 80px;
      right: auto;
      left: 15px;
      width: 220px;
      max-height: 300px;
    }
  }
</style>

<!-- Bell toggle button -->
<div
  id="notificationToggleBtn"
  class="notification-toggle-btn <?php echo $newAlerts ? 'glow' : ''; ?> "
  onclick="toggleNotificationDropdown()"
  title="Notifications"
  aria-label="Notifications"
  role="button"
  tabindex="0"
  onkeydown="if(event.key==='Enter'){ toggleNotificationDropdown(); }"
>
  <i id="notificationIcon" class="bi bi-bell"></i>
</div>

<!-- Dropdown -->
<div id="notificationDropdown" class="notification-dropdown" aria-live="polite" aria-atomic="true">
  <?php if (empty($notifications)): ?>
    <div class="notification-empty">No notifications to show</div>
  <?php else: ?>
    <?php foreach ($notifications as $notif): ?>
      <?php
        $notifId = htmlspecialchars($notif['id']);
        $message = htmlspecialchars($notif['message']);
        $type = $notif['type'];
      ?>
      <div 
        id="<?php echo $notifId; ?>"
        class="notification-item unread" 
        data-notif-id="<?php echo $notifId; ?>"
        onclick="
          <?php if ($type === 'stock'): ?>
            viewNotification('<?php echo $notifId; ?>', 'stock', '<?php echo urlencode($notif['item_id']); ?>', '<?php echo urlencode($notif['supplier_code']); ?>');
          <?php elseif ($type === 'lab'): ?>
            viewNotification('<?php echo $notifId; ?>', 'lab', '<?php echo urlencode($notif['test_id']); ?>');
          <?php endif; ?>
        "
        title="Click to view details"
      >
        <?php echo $message; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
  function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    const icon = document.getElementById('notificationIcon');
    const isActive = dropdown.classList.toggle('active');

    icon.classList.toggle('bi-bell', !isActive);
    icon.classList.toggle('bi-x', isActive);
  }

  document.addEventListener('click', function(e) {
    const toggleBtn = document.getElementById('notificationToggleBtn');
    const dropdown = document.getElementById('notificationDropdown');
    const icon = document.getElementById('notificationIcon');

    if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
      if (dropdown.classList.contains('active')) {
        dropdown.classList.remove('active');
        icon.classList.remove('bi-x');
        icon.classList.add('bi-bell');
      }
    }
  });

  function viewNotification(notifId, type, param1 = '', param2 = '') {
    const notifElem = document.getElementById(notifId);
    if (notifElem) {
      notifElem.classList.remove('unread');
      notifElem.classList.add('read');
    }

    let readNotifs = JSON.parse(localStorage.getItem('readNotifications') || '[]');
    if (!readNotifs.includes(notifId)) {
      readNotifs.push(notifId);
      localStorage.setItem('readNotifications', JSON.stringify(readNotifs));
    }

    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    if (unreadCount === 0) {
      document.getElementById('notificationToggleBtn').classList.remove('glow');
    }

    if (type === 'stock') {
      window.location.href = '/Dental_System/modules/inventory_functions/view_inventory.php?id=' 
        + encodeURIComponent(param1) 
        + '&supplier_code=' + encodeURIComponent(param2);
    } else if (type === 'lab') {
      window.location.href = '/Dental_System/modules/lab_functions/test_request/view_test_request.php?id=' 
        + encodeURIComponent(param1);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const readNotifs = JSON.parse(localStorage.getItem('readNotifications') || '[]');
    readNotifs.forEach(id => {
      const elem = document.getElementById(id);
      if (elem) {
        elem.classList.remove('unread');
        elem.classList.add('read');
      }
    });

    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    if (unreadCount === 0) {
      document.getElementById('notificationToggleBtn').classList.remove('glow');
    }
  });
</script>
