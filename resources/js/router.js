import { createRouter, createWebHistory } from 'vue-router';

// Import pages
import Login from './pages/Login.vue';
import Dashboard from './pages/Dashboard.vue';
import PendingTickets from './pages/PendingTickets.vue';
import EditTicket from './pages/EditTicket.vue'
import ResolveClientTicket from './pages/ResolveClientTicket.vue';
import ResolveDashboard from './pages/ResolveDashboard.vue';
import TicketRequestDashboard from './pages/TicketRequest.vue';
import AddNewTicket from './pages/AddNewTicket.vue';
import ClientAddTicket from './pages/ClientAddTicket.vue';
import ReportPage from './pages/ReportPage.vue';
import axios from 'axios';

const routes = [
  { path: '/', name: 'ClientAddTicket', component: ClientAddTicket },
  { path: '/login', name: 'Login', component: Login },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true, title: 'Dashboard' }},
  { path: '/pendingtickets', name: 'PendingTickets', component: PendingTickets, meta: { requiresAuth: true, title: 'Pending Tickets' }},
  { path: '/tickets/:id/edit', name: 'EditTicket', component: EditTicket, props: true, meta: { requiresAuth: true, title: 'Edit Ticket' }},
  { path: '/tickets/:id/resolve', name: 'ResolveClientTicket', component: ResolveClientTicket, props: true, meta: { requiresAuth: true, title: 'Resolve Client Ticket' }},
  { path: '/resolvedtickets', name: 'ResolveDashboard', component: ResolveDashboard, meta: { requiresAuth: true, title: 'Resolve Tickets' }},
  { path: '/ticketrequestview', name: 'TicketRequest', component: TicketRequestDashboard, meta: { requiresAuth: true, title: 'Ticket Request View' }},
  { path: '/reports', name: 'ReportPage', component: ReportPage, meta: { requiresAuth: true, title: 'Reports Page' }},
  { path: '/newticket', name: 'AddNewTicket', component: AddNewTicket, meta: { requiresAuth: true, title: 'Add New Ticket' }},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  linkActiveClass: "text-blue-600 font-bold",
  linkExactActiveClass: "bg-gray-200 text-blue-600 font-bold",
});

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth) {
    try {
      const res = await axios.get('/api/user', { withCredentials: true });
      console.log('API USER response:', res.data);
      if (res.data) next();
    } catch (err) {
      console.log('Unauthorized:', err.response?.data);
      next('/login');
    }
  } else {
    next();
  }
});

export default router;
