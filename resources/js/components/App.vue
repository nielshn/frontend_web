<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import axios from 'axios';

// STATE
const show = ref(false);
const notifications = ref([]);
const connected = ref(false);
const unread = ref(0);
const loading = ref(false);
const markingAllRead = ref(false);
const token = ref(null);

// HEADERS
const headers = ref({ Authorization: '' });

// PERBARUI HEADER SAAT TOKEN BERUBAH
watch(token, (newToken) => {
  if (newToken) {
    headers.value.Authorization = `Bearer ${newToken}`;
    getNotifications();
  }
});

// TIME AGO
const timeAgo = (date) => {
  const mins = Math.floor((new Date() - new Date(date)) / 60000);
  if (mins < 1) return 'baru';
  if (mins < 60) return `${mins}m`;
  if (mins < 1440) return `${Math.floor(mins / 60)}j`;
  return `${Math.floor(mins / 1440)}h`;
};

// GET NOTIFICATIONS

const getNotifications = async () => {
  if (loading.value || !token.value) return;
  console.log('🔄 Memulai fetch notifikasi...');
  loading.value = true;
  try {
    const { data } = await axios.get('http://127.0.0.1:8090/api/notifikasis?limit=15', {
      headers: headers.value
    });

    console.log('✅ Notifikasi diterima:', data);
    notifications.value = (data?.data || data || []).map(n => ({
      ...n,
      read: n.read === 1 || n.read === true,
      time: timeAgo(n.created_at),
    }));
    unread.value = notifications.value.filter(n => !n.read).length;
  } catch (e) {
    console.error('❌ Gagal memuat notifikasi:', e);
    toast.error('Gagal memuat notifikasi');
  } finally {
    loading.value = false;
  }
};

// MARK SINGLE
const markRead = async (id) => {
  const index = notifications.value.findIndex(n => n.id === id);
  if (index === -1 || notifications.value[index].read) return;

  const original = { ...notifications.value[index] };
  notifications.value[index].read = true;
  unread.value--;

  try {
    await axios.put(`http://127.0.0.1:8090/api/notifikasis/${id}/read`, {}, {
      headers: headers.value,
    });
  } catch (e) {
    notifications.value[index] = original;
    unread.value++;
    toast.error('Gagal menandai notifikasi sebagai dibaca');
  }
};

// MARK ALL
const markAllRead = async () => {
  if (markingAllRead.value || unread.value === 0) return;

  const original = [...notifications.value];
  notifications.value = notifications.value.map(n => ({ ...n, read: true }));
  unread.value = 0;
  markingAllRead.value = true;

  try {
    await axios.put('http://127.0.0.1:8090/api/notifikasi/read-all', {}, {
      headers: headers.value,
    });
    toast.success('Semua notifikasi ditandai sebagai dibaca');
  } catch (e) {
    notifications.value = original;
    unread.value = original.filter(n => !n.read).length;
    toast.error('Gagal menandai semua notifikasi');
  } finally {
    markingAllRead.value = false;
  }
};

// HANDLE BROADCAST
const handleNotification = (data) => {
  const n = {
    ...data,
    id: data.id || Date.now(),
    time: 'baru',
    read: false,
  };
  notifications.value.unshift(n);
  unread.value++;
  toast(`${n.title}: ${n.message}`, {
    type: 'warning',
    autoClose: 4000,
    position: 'top-right'
  });
};

// KLIK NOTIF
const handleNotificationClick = (notif) => {
  if (!notif.read) markRead(notif.id);
};

// TOGGLE DROPDOWN
const toggle = () => show.value = !show.value;
const closeDropdown = () => show.value = false;

// CLICK OUTSIDE
const handleClickOutside = (event) => {
  if (show.value && !event.target.closest('.notification-wrapper')) {
    closeDropdown();
  }
};

// MOUNTED
let echo = null;
let intervalId = null;

onMounted(() => {
  setTimeout(() => {
    token.value = window.laravelToken || localStorage.getItem('token');
    if (token.value) {
      localStorage.setItem('token', token.value);
    } else {
      toast.error('Token tidak ditemukan. Silakan login kembali.');
      return;
    }
  }, 300); // Delay agar token dari Laravel sempat dimasukkan

  if (window.Echo) {
    window.Echo.connector.pusher.connection.bind('connected', () => connected.value = true);
    window.Echo.connector.pusher.connection.bind('disconnected', () => connected.value = false);

    echo = window.Echo.channel('stock-channel')
      .listen('.stock.minimum', handleNotification);
  }

  intervalId = setInterval(() => {
    if (headers.value.Authorization) getNotifications();
  }, 60000);

  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  if (echo) echo.stopListening('.stock.minimum');
  if (intervalId) clearInterval(intervalId);
  document.removeEventListener('click', handleClickOutside);
});
</script>


<template>
    <div class="notification-wrapper">
        <button @click="toggle" class="notification-btn" :title="`${unread} notifikasi`">
            <i class="ph-bell"></i>
            <span v-if="unread" class="badge">{{ unread > 9 ? '9+' : unread }}</span>
            <span class="status" :class="{ connected }"></span>
        </button>

        <div v-if="show" class="notification-dropdown">
            <!-- Header -->
            <div class="header">
                <span>Notifikasi</span>
                <div class="header-actions">
                    <button @click="getNotifications" :disabled="loading" class="refresh-btn" title="Refresh">
                        <i :class="loading ? 'ph-spinner ph-spin' : 'ph-arrow-clockwise'"></i>
                    </button>
                    <button @click="closeDropdown" class="close-btn" title="Tutup">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>

            <!-- List -->
            <div class="list">
                <div v-if="loading && !notifications.length" class="loading">
                    <div class="spinner"></div>
                    <span>Memuat notifikasi...</span>
                </div>

                <div v-else-if="notifications.length" class="items">
                    <div v-for="n in notifications" :key="n.id" class="item" :class="{ unread: !n.read }"
                        @click="handleNotificationClick(n)">
                        <div class="icon" :class="{ unread: !n.read }">
                            <i class="ph-warning"></i>
                        </div>
                        <div class="content">
                            <div class="title">{{ n.title }}</div>
                            <div class="message">{{ n.message }}</div>
                        </div>
                        <div class="meta">
                            <span class="time">{{ n.time }}</span>
                            <span v-if="!n.read" class="dot"></span>
                        </div>
                    </div>
                </div>

                <div v-else class="empty">
                    <i class="ph-bell-slash"></i>
                    <span>Belum ada notifikasi</span>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="unread > 0" class="footer">
                <button @click="markAllRead" :disabled="markingAllRead" class="mark-all-btn">
                    <i v-if="markingAllRead" class="ph-spinner ph-spin"></i>
                    {{ markingAllRead ? 'Memproses...' : 'Tandai semua dibaca' }}
                </button>
            </div>
        </div>

        <!-- Overlay -->
        <div v-if="show" class="overlay" @click="closeDropdown"></div>
    </div>
</template>

<style scoped>
.notification-wrapper {
    position: relative;
}

.notification-btn {
    position: relative;
    background: none;
    border: none;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.2s;
}

.notification-btn:hover {
    background: rgba(0, 0, 0, 0.05);
}

.notification-btn i {
    font-size: 20px;
    color: #6c757d;
}

.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #dc3545;
    color: white;
    font-size: 10px;
    font-weight: 600;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.status {
    position: absolute;
    bottom: 0;
    right: 2px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ffc107;
    transition: background 0.2s;
}

.status.connected {
    background: #28a745;
}

.notification-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 300px;
    max-height: 400px;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
    font-size: 14px;
    background: #f8f9fa;
}

.header-actions {
    display: flex;
    gap: 4px;
}

.refresh-btn,
.close-btn {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    border-radius: 4px;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

.refresh-btn:hover,
.close-btn:hover {
    background: rgba(0, 0, 0, 0.1);
}

.refresh-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.list {
    max-height: 280px;
    overflow-y: auto;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
    gap: 8px;
}

.loading span {
    font-size: 12px;
    color: #6c757d;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #e9ecef;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.items {
    display: flex;
    flex-direction: column;
}

.item {
    display: flex;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.2s;
    border-bottom: 1px solid #f8f9fa;
    position: relative;
}

.item:hover {
    background: #f8f9fa;
    transform: translateX(2px);
}

.item:last-child {
    border-bottom: none;
}

.item.unread {
    background: rgba(0, 123, 255, 0.05);
    border-left: 3px solid #007bff;
}

.item.unread:hover {
    background: rgba(0, 123, 255, 0.1);
}

.icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #6c757d;
    color: white;
    font-size: 12px;
    flex-shrink: 0;
    margin-right: 12px;
}

.icon.unread {
    background: #007bff;
}

.content {
    flex: 1;
    min-width: 0;
}

.title {
    font-size: 13px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message {
    font-size: 12px;
    color: #6c757d;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: 8px;
}

.time {
    font-size: 11px;
    color: #adb5bd;
    margin-bottom: 4px;
}

.dot {
    width: 6px;
    height: 6px;
    background: #007bff;
    border-radius: 50%;
}

.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 16px;
    color: #adb5bd;
}

.empty i {
    font-size: 32px;
    margin-bottom: 8px;
}

.empty span {
    font-size: 13px;
}

.footer {
    padding: 12px 16px;
    border-top: 1px solid #e9ecef;
    text-align: center;
    background: #f8f9fa;
}

.mark-all-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 100%;
}

.mark-all-btn:hover:not(:disabled) {
    background: #0056b3;
}

.mark-all-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.overlay {
    display: none;
}

/* Custom scrollbar */
.list::-webkit-scrollbar {
    width: 4px;
}

.list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .notification-dropdown {
        position: fixed;
        top: 60px !important;
        left: 12px;
        right: 12px;
        width: auto;
        max-height: calc(100vh - 80px);
    }

    .overlay {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 999;
    }

    .item {
        padding: 16px;
    }

    .icon {
        width: 32px;
        height: 32px;
        margin-right: 16px;
    }

    .title {
        font-size: 14px;
    }

    .message {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .notification-dropdown {
        left: 8px;
        right: 8px;
    }
}

/* Animation untuk notifikasi baru */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.item {
    animation: slideIn 0.3s ease-out;
}

/* Pulse animation untuk tombol notifikasi ketika ada notifikasi baru */
@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
    }
}

.notification-btn:has(.badge) {
    animation: pulse 2s infinite;
}
</style>
