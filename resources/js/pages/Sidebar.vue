<template>
  <aside
    :class="[
      'fixed inset-y-0 flex-wrap items-center justify-between block w-full p-0 my-4 overflow-y-auto antialiased transition-transform duration-200 bg-white border-0 shadow-xl dark:shadow-none dark:bg-slate-850 max-w-64 ease-nav-brand z-990 xl:ml-6 rounded-2xl',
      isOpen ? 'translate-x-0' : '-translate-x-full',
      'xl:left-0 xl:translate-x-0' 
    ]"
  >
    <div class="h-19">
      <a class="block px-8 py-6 m-0 text-sm whitespace-nowrap dark:text-white text-slate-700" href="#">
        <img src="/img/official-logo-cropped.png" class="inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-8" alt="main_logo" />
        <span class="ml-1 font-semibold transition-all duration-200 ease-nav-brand">iSupport Ticketing</span>
      </a>
    </div>

    <hr class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

    <div class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full">
      <!-- Main menu -->
      <ul class="flex flex-col pl-0 mb-0">
        <li
          v-for="item in menuItems"
          :key="item.name"
          class="mt-0.5 w-full"
        >
          <router-link
            :to="item.link"
            class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors"
            active-class="bg-gray-200 text-blue-600 font-bold"
            exact
            @click="$emit('close')"
          >
            <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
              <i :class="[item.icon, item.color]"></i>
            </div>
            <span class="ml-1">{{ item.name }}</span>
          </router-link>
        </li>
      </ul>
      <!-- CMS Product Management -->
      <ul v-if="userRole === '1'" class="flex flex-col pl-0 mt-4 mb-0">
          <li>
            <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
              CMS
            </h6>
          </li>
          <li
            v-for="item in productItems"
            :key="item.name"
            class="mt-0.5 w-full"
          >
            <a
              :href="item.link"
              class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors"
              @click="$emit('close')"
            >
              <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                <i :class="[item.icon, item.color]"></i>
              </div>
              <span class="ml-1">{{ item.name }}</span>
            </a>
          </li>
        </ul>
      <!-- Account section -->
      <ul class="flex flex-col pl-0 mt-4 mb-0">
        <li>
          <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
            Account
          </h6>
        </li>
        <li
          v-for="item in accountItems"
          :key="item.name"
          class="mt-0.5 w-full"
        >
          <a
            href="#"
            class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors"
            @click.prevent="handleClick(item)"
          >
            <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
              <i :class="[item.icon, item.color]"></i>
            </div>
            <span class="ml-1">{{ item.name }}</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";

export default {
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    menuItems: {
      type: Array,
      default: () => [
        { name: "Dashboard", link: "/dashboard", icon: "ni ni-tv-2", color: "text-blue-500" },
        { name: "Add New Ticket", link: "/newticket", icon: "ni ni-fat-add", color: "text-orange-500" },
        { name: "Pending Tickets", link: "/pendingtickets", icon: "ni ni-time-alarm", color: "text-orange-500" },
        { name: "Resolved Tickets", link: "/resolvedtickets", icon: "ni ni-check-bold", color: "text-emerald-500" },
        { name: "Ticket Request View", link: "/ticketrequestview", icon: "ni ni-app", color: "text-cyan-500" },
        { name: "Reports", link: "/reports", icon: "ni ni-world-2", color: "text-red-600" }
      ]
    },
    productItems: {
      type: Array,
      default: () => [
        { name: "Product Management", link: "/productmanagement", icon: "ni ni-box-2", color: "text-emerald-500" },
      ]
    },
    accountItems: {
      type: Array,
      default: () => [
        { name: "Sign Out", link: "/logout", icon: "ni ni-button-power", color: "text-orange-500" }
      ]
    }
  },
  setup(props, { emit }) {
    const userRole = ref('0');

    const fetchUser = async () => {
      try {
        const res = await axios.get('/api/user', { withCredentials: true });
        userRole.value = res.data.role;
      } catch (err) {
        console.error("Failed to fetch user role", err);
      }
    };

    const handleClick = async (item) => {
      emit('close'); 
      if (item.name === "Sign Out") {
        try {
          await axios.post('/logout', {}, { withCredentials: true });
          window.location.href = '/login'; 
        } catch (err) {
          console.error("Logout failed", err);
        }
      } else {
        window.location.href = item.link;
      }
    };

    onMounted(() => {
      fetchUser();
    });

    return {
      userRole,
      handleClick
    };
  }
};
</script>

