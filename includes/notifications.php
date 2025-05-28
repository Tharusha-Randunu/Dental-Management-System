<?php
// Sample notifications (replace with your DB fetch)
$notifications = [
    ['id'=>1, 'message'=>'New appointment scheduled', 'read'=>false],
    ['id'=>2, 'message'=>'Inventory stock low', 'read'=>true],
];
$newAlerts = false;
foreach ($notifications as $notif) {
    if (!$notif['read']) {
        $newAlerts = true;
        break;
    }
}
?>

<style>
  /* Bell toggle button */
  .notification-toggle-btn {
    position: fixed;
    top: 20px;
    right: 80px; /* place it left of your sidebar toggle */
    font-size: 26px;
    color: #0d47a1;
    background-color: #fff;
    padding: 10px 12px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    z-index: 1000; /* above sidebar */
    transition: background-color 0.3s ease;
  }

  
  .notification-toggle-btn:hover {
    background-color: #bbdefb;
  }

  /* Glow effect for new alerts */
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

  /* Dropdown container */
  .notification-dropdown {
    position: fixed;
    top: 60px; /* below toggle button */
    right: 80px;
    width: 280px;
    max-height: 350px;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    border-radius: 6px;
    overflow-y: auto;
    display: none;
    z-index: 950;
    font-family: Arial, sans-serif;
  }
  .notification-dropdown.active {
    display: block;
  }

  /* Notifications */
  .notification-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #333;
  }
  .notification-item.unread {
    background-color: #e3f2fd;
    font-weight: 600;
  }
  .notification-item:last-child {
    border-bottom: none;
  }

  /* Empty notification */
  .notification-empty {
    padding: 20px;
    text-align: center;
    color: #888;
    font-style: italic;
  }

  /* Responsive */
  @media (max-width: 576px) {
    .notification-toggle-btn {
      top: 15px;
      right: auto;      /* Remove right positioning */
      left: 15px;       /* Position on the left */
      font-size: 26px;  /* Keep normal size */
      padding: 10px 12px; /* Keep original padding */
    }
    .notification-dropdown {
      top: 80px;
      right: auto;       /* Remove right positioning */
      left: 15px;        /* Align dropdown with button on left */
      width: 220px;
      max-height: 300px;
    }
  }
</style>

<!-- Bell toggle button -->
<div
  id="notificationToggleBtn"
  class="notification-toggle-btn <?php echo $newAlerts ? 'glow' : ''; ?>"
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
    <div class="notification-empty">No notifications</div>
  <?php else: ?>
    <?php foreach ($notifications as $notif): ?>
      <div class="notification-item <?php echo $notif['read'] ? '' : 'unread'; ?>">
        <?php echo htmlspecialchars($notif['message']); ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
  function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    const icon = document.getElementById('notificationIcon');
    const isActive = dropdown.classList.toggle('active');

    if (isActive) {
      icon.classList.remove('bi-bell');
      icon.classList.add('bi-x');
    } else {
      icon.classList.remove('bi-x');
      icon.classList.add('bi-bell');
    }
  }

  // Close dropdown on outside click
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
</script>
