<template>
    <div class="notifications-container">
      <button @click="toggleList" class="notification-btn">
        🔔
        <span v-if="notifications.length > 0" class="notfication-badge">
          {{ notifications.length }}
        </span>
      </button>
  
      <div v-if="showList" class="notification-list">
        <div v-for="notification in notifications" :key="notification.id" class="notification-item">
          {{ notification.message }}
        </div>
        <button @click="markAllAsRead" class="mark-read-btn">
          Marcar como leído
        </button>
      </div>
    </div>
  </template>
  
  <script>
  
  export default {
    data() {
      return {
        notifications: [],
        showList: false
      };
    },
    methods: {
      fetchNotifications() {
        window.axios.get('/api/notifications')
          .then(response => {
            this.notifications = response.data;
          });
      },
      markAllAsRead() {
        window.axios.post('/api/notifications/read')
          .then(() => {
            this.notifications = [];
            this.showList = false;
          });
      },
      toggleList() {
        this.showList = !this.showList;
      }
    },
    mounted() {
      this.fetchNotifications();
    }
  };
  </script>
  