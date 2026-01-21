<template>
      <body class="m-0 font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>

    <div class="flex">
        <Sidebar />
        <main class="flex-1 ml-64">
        </main>
    </div>

    <main class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl">
      <Navbar />

      <div class="w-full px-6 py-6 mx-auto">
        <!-- Filter Table -->
        <div class="p-4 mb-4 bg-white dark:bg-slate-850 rounded-2xl shadow-md">
          <div class="flex flex-wrap -mx-3 items-end space-x-2">
            <!-- Date From -->
            <div class="flex-1 px-3">
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Date From</label>
              <input
                type="date"
                v-model="filters.dateFrom"
                :max="filters.dateTo"
                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 block w-full appearance-none rounded-lg border border-gray-250 bg-white px-3 py-2 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500"
              />
            </div>

            <!-- Date To -->
            <div class="flex-1 px-3">
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Date To</label>
              <input
                type="date"
                v-model="filters.dateTo"
                :min="filters.dateFrom"
                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 block w-full appearance-none rounded-lg border border-gray-250 bg-white px-3 py-2 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500"
              />
            </div>

            <!-- Engineer Name -->
             <div class="flex-1 px-3 relative">
                      <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                          Engineer
                        </label>

                        <multiselect
                          v-model="filters.engineer"
                          :options="filteredEngineers"
                          label="engineer"
                          track-by="engineer"
                          placeholder="Select engineer..."
                          :allow-empty="true"
                          :close-on-select="false"
                          :clear-on-select="false"
                          :show-labels="false"
                          class="w-full text-sm consistent-multiselect"
                          :multiple="true"
                        ></multiselect>
                      </div>

            <div class="px-3 flex items-end">
              <button
                @click.prevent="handleFilter"
                class="btn-gradient-purple "
              >
                Filter
              </button>
            </div>
          </div>
        </div>
         
        <!-- table 1 -->
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-full max-w-full px-3">
              <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">

                <!-- Header -->
                <div class="p-4 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent mb-2"> 
                  <div class="flex items-center flex-none w-1/2 max-w-full px-3 mb-2"> 
                    <h6 class="mb-0 dark:text-white">Pending Tickets</h6> 
                  </div> 
                  <div class="flex flex-wrap -mx-3"> 
                    <div class="flex items-center flex-none w-1/2 max-w-full px-3"> 
                      <ExportTickets
                        :tickets="tickets"
                        :filters="filters"
                        title="iSupport Ticketing System - Pending Tickets"
                        :headers="[
                          'Date','Ticket #','Severity','Engineer','Product Line',
                          'License','Reseller Name','Serial','Service Partner',
                          'SAP DB','Infrastructure','SAP Version','Company', 'Attachment',
                          'Contact Name','Contact Number','Concern'
                        ]"
                       :columns="[
                          { key: 'date_created', map: (ticket) => ticket.date_created || 'N/A' },
                          { key: 'ticket_number', map: (ticket) => ticket.ticket_number || 'N/A' },
                          { key: 'severity', map: (ticket) => ticket.severity || 'N/A' },
                          { key: 'engineer_assigned', map: (ticket) => ticket.engineer_assigned?.assign_name || 'N/A' },
                          { key: 'product_line', map: (ticket) => ticket.product_line?.prod_name || 'N/A' },
                          { key: 'License', map: (ticket) => ticket.License || 'N/A' },
                          { key: 'Reseller_name', map: (ticket) => ticket.Reseller_name || 'N/A' },
                          { key: 'serial_number', map: (ticket) => ticket.serial_number || 'N/A' },
                          { key: 'partner_name', map: (ticket) => ticket.partner_name || 'N/A' },
                          { key: 'sapdatabase_name', map: (ticket) => ticket.sapdatabase_name || 'N/A' },
                          { key: 'infrastructure', map: (ticket) => ticket.infrastructure || 'N/A' },
                          { key: 'sap_version', map: (ticket) => ticket.sap_version || 'N/A' },
                          { key: 'company_name', map: (ticket) => ticket.company_name || 'N/A' },
                          { key: 'attachment', map: (ticket) => ticket.attachment || 'N/A' },
                          { key: 'contact_name', map: (ticket) => ticket.contact_name || 'N/A' },
                          { key: 'contact_number', map: (ticket) => ticket.contact_number || 'N/A' },
                          { key: 'concern', map: (ticket) => ticket.concern || 'N/A' }
                        ]"
                      />
                  </div> 
                    <div class="flex-none w-1/2 max-w-full px-3 flex gap-2 justify-end"> 
                      <input
                      v-model="globalSearch"
                      @input="debouncedSearch"
                      type="text"
                      placeholder="Search tickets..."
                      class="rounded-lg border border-gray-300 px-3 py-1 text-sm w-64"
                    />
                    </div> 
                  </div> 
                </div>
                <!-- Table -->
                <div class="flex-auto px-0 pt-0 pb-2 overflow-x-auto">
                   <div v-if="loading" class="text-center py-4">
                      <span class="text-gray-500">Loading tickets...</span>
                    </div>
                  <table v-else class="items-center w-full mb-0 align-top border-collapse text-slate-500 dark:border-white/40">
                    <thead class="align-bottom">
                      <tr>
                        <th
                          v-for="col in columns"
                          :key="col.key"
                          :class="[
                            'px-6 py-3 font-bold uppercase bg-transparent border-b border-b-solid border-slate-100 text-xs text-slate-400 opacity-50 whitespace-nowrap',
                            alignClass(col.align)
                          ]"
                        >
                          {{ col.label }}
                        </th>
                      </tr>
                    </thead>

                    <tbody>
                     <tr v-for="ticket in tickets" :key="ticket.ticket_id" @click="openModal(ticket)" class="table-row-clickable">
                        <td
                          v-for="col in columns"
                          :key="col.key"
                          :class="[
                            'p-2 bg-transparent border-b border-slate-100 whitespace-nowrap',
                            alignClass(col.align)
                          ]"
                        >
                          <!-- User cell -->
                          <template v-if="col.type === 'user'">
                            <div :class="col.align === 'center' ? 'text-center' : ''">
                              <h6 class="mb-0 text-sm leading-normal dark:text-white">
                                {{ col.formatter ? col.formatter(ticket).name : ticket[col.key]?.name }}
                              </h6>
                              <p class="mb-0 text-xs text-slate-400 dark:opacity-80">
                                {{ col.formatter ? col.formatter(ticket).email : ticket[col.key]?.email }}
                              </p>
                            </div>
                          </template>

                          <!-- Badge cell (severity) -->
                          <template v-else-if="col.type === 'badge'">
                            <span
                              :class="[
                                'px-2.5 py-1.4 text-xs rounded-1.8 inline-block whitespace-nowrap font-bold uppercase leading-none text-white',
                                severityClass(ticket[col.key])
                              ]"
                            >
                              {{ ticket[col.key] }}
                            </span>
                          </template>

                          <!-- Date cell -->
                          <template v-else-if="col.type === 'date'">
                            <span class="text-xs font-semibold leading-tight text-slate-400 dark:text-white dark:opacity-80">
                              {{ formatDate(ticket[col.key]) }}
                            </span>
                          </template>

                          <!-- Actions cell -->
                          <template v-else-if="col.type === 'action'">
                            <a href="javascript:;" class="text-xs font-semibold leading-tight text-slate-400 dark:text-white dark:opacity-80">
                              Edit
                            </a>
                          </template>

                          <!-- Default text cell -->
                          <template v-else>
                            <span class="text-sm dark:text-white">
                              {{ col.formatter ? col.formatter(ticket) : ticket[col.key] }}
                            </span>
                          </template>
                        </td>
                      </tr>

                      <tr v-if="tickets.length === 0">
                        <td :colspan="columns.length" class="text-center py-4 text-slate-400">
                          No tickets found.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                 <BaseModal :isOpen="isModalOpen" @close="closeModal">
                    <template #header>
                      <h4>Ticket Number: {{ selectedTicket?.ticket_number }}</h4>
                    </template>
                    <div class="ticket-details">
                      <p class="mt-2"><span class="font-semibold">Date Created:</span> {{ formatDate(selectedTicket?.date_created) }}</p>
                      <p class="mt-2"><span class="font-semibold">Severity:</span> {{ selectedTicket?.severity }}</p>
                      <p class="mt-2"><span class="font-semibold">Engineer Assigned:</span> {{ selectedTicket?.engineer_assigned?.assign_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Product Line:</span> {{ selectedTicket?.product_line?.prod_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">License/Activation Code:</span> {{ selectedTicket?.License || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Reseller Name:</span> {{ selectedTicket?.Reseller_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Serial Number:</span> {{ selectedTicket?.serial_number || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Service Partner:</span> {{ selectedTicket?.partner_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Sap B1 Database:</span> {{ selectedTicket?.sapdatabase_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Infrastracture:</span> {{ selectedTicket?.infrastructure || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Sap B1 Version:</span> {{ selectedTicket?.sap_version || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Company Name:</span> {{ selectedTicket?.company_name || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Contact Name:</span> {{ selectedTicket?.contact_name || 'N/A' }} </p>
                      <p class="mt-2"><span class="font-semibold">Contact Email:</span> {{ selectedTicket?.contact_email || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Contact Number:</span> {{ selectedTicket?.contact_number || 'N/A' }}</p>
                      <p class="mt-2"><span class="font-semibold">Concern:</span> {{ selectedTicket?.concern || 'N/A' }}</p>
                      <div class="mt-2">
                        <span class="font-semibold">Attachments:</span>
                        <div v-if="attachmentList.length">
                          <ul class="list-disc ml-5">
                           <li v-for="(file, index) in attachmentList" :key="index">
                            <button
                              @click="downloadFile(file.filename || file)"
                              class="text-blue-500 hover:underline"
                            >
                              {{ shortenName(file.filename || file) }}
                            </button>
                          </li>
                          </ul>
                        </div>
                        <span v-else>N/A</span>
                      </div>

                    </div>
                    <template #footer>
                        <button
                            v-if="authUser && canEditTicket(selectedTicket)"
                            class="btn-gradient-blue"
                            @click="editTicket(selectedTicket)"
                          >
                            Edit
                          </button>
                      <button class="btn-gradient-yellow" @click="resolveTicket(selectedTicket)">Resolve</button>
                    </template>
                  </BaseModal>
                    <!-- Pagination + Total -->
                <div class="flex items-center justify-between mt-4 p-2 bg-white dark:bg-slate-850 rounded-xl shadow-sm">
                  <div class="text-sm text-slate-700 dark:text-white/80">
                    Total Results: <span class="font-semibold">{{ pagination.total }}</span>
                  </div>

                  <div class="flex items-center space-x-1">
                    <button
                      @click="changePage(pagination.current_page - 1)"
                      :disabled="pagination.current_page === 1"
                      class="px-4 py-2 rounded-xl bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 transition"
                    >
                      Prev
                    </button>

                    <button
                      v-for="page in visiblePages"
                      :key="page"
                      @click="typeof page === 'number' && changePage(page)"
                      :disabled="page === '...'"
                      :class="[
                        'px-4 py-2 rounded-xl hover:bg-gray-100 transition dark:hover:bg-slate-600',
                        page === pagination.current_page 
                          ? 'bg-blue-500 text-white dark:bg-blue-600' 
                          : 'bg-white text-gray-700 dark:bg-slate-700 dark:text-white'
                      ]"
                    >
                      {{ page }}
                    </button>

                    <button
                      @click="changePage(pagination.current_page + 1)"
                      :disabled="pagination.current_page === pagination.last_page"
                      class="px-4 py-2 rounded-xl bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 transition"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <Footer />
      </div>
    </main>
  </body>
</template>


<script>
import BaseModal from '../pages/BaseModal.vue';
import Sidebar from '../pages/Sidebar.vue';
import Navbar from '../pages/Navbar.vue';
import Footer from '../pages/Footer.vue';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import ExportTickets from "../export/ExportTickets.vue";
import axios from "axios";

export default {
  name: "App",
  components: { Sidebar, Navbar, Footer, BaseModal, Multiselect, ExportTickets },
  data() {
    return {
      currentYear: new Date().getFullYear(),
      columns: [
        { key: 'date_created', label: 'Date Created', type: 'date', align: 'center', formatter: (ticket) => ticket.date_created || 'N/A' },
        { key: 'ticket_number', label: 'Ticket#', type: 'text', align: 'center', formatter: (ticket) => ticket.ticket_number || 'N/A' },
        { key: 'severity', label: 'Severity', type: 'badge', align: 'center' },
        { key: 'assign_name', label: 'Engineer', type: 'text', align: 'center', formatter: (ticket) => ticket.engineer_assigned?.assign_name || 'N/A' },
        { key: 'prod_name', label: 'Product Line', type: 'text', align: 'center', formatter: (ticket) => ticket.product_line?.prod_name || 'N/A' },
        { key: 'License', label: 'License/Activation Code', type: 'text', align: 'center', formatter: (ticket) => ticket.License || 'N/A' },
        { key: 'Reseller_name', label: 'Reseller Name', type: 'text', align: 'center', formatter: (ticket) => ticket.Reseller_name || 'N/A' },
        { key: 'serial_number', label: 'Serial Number', type: 'text', align: 'center', formatter: (ticket) => ticket.serial_number || 'N/A' },
        { key: 'partner_name', label: 'Service Partner', type: 'text', align: 'center', formatter: (ticket) => ticket.partner_name || 'N/A' },
        { key: 'company_name', label: 'Company Name', type: 'text', align: 'center', formatter: (ticket) => ticket.company_name || 'N/A' },
        { key: 'contact_name', label: 'Contact Name/Email', type: 'user', align: 'left', 
          formatter: (ticket) => {
            return {
              name: ticket.contact_name || '', 
              email: ticket.contact_email || 'N/A' 
            };
        } }, 
        { key: 'contact_number', label: 'Contact Number', type: 'text', align: 'center', formatter: (ticket) => ticket.contact_number || 'N/A' }
      ],

      tickets: [], 
       filters: {
       dateFrom: '',
       dateTo: '',
       engineer: ""
    },
       pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      },
      filteredEngineers: [] ,
      engineers: [],
      isModalOpen: false,
      selectedTicket: null,
      loading: false,
      baseUrl: '',
      globalSearch: '',
      debounceTimer: null,
      authUser: null,
    };
  },
computed: {
   filteredTickets() {
    const from = this.filters.dateFrom ? new Date(this.filters.dateFrom) : null;
    const to = this.filters.dateTo ? new Date(this.filters.dateTo) : null;

    return this.tickets.filter(ticket => {
      const ticketDate = new Date(ticket.date_created);
      const engineerMatch = this.filters.engineer
        ? ticket.engineer?.toLowerCase().includes(this.filters.engineer.toLowerCase())
        : true;

      const dateMatch = (!from || ticketDate >= from) && (!to || ticketDate <= to);

      return engineerMatch && dateMatch;
    });
  },
  visiblePages() {
    const total = this.pagination.last_page;
    const current = this.pagination.current_page;
    const delta = 2;
    const range = [];
    for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
      range.push(i);
    }
    if (current - delta > 2) range.unshift("...");
    if (current + delta < total - 1) range.push("...");
    range.unshift(1);
    if (total > 1) range.push(total);
    return range;
  },
    attachmentList() {
    if (!this.selectedTicket?.attachment) return [];
    return this.selectedTicket.attachment.split(',').map(f => f.trim());
  },

},
methods: {
  debouncedSearch() {
    clearTimeout(this.debounceTimer);
    this.debounceTimer = setTimeout(() => {
      this.fetchTickets(1);
    }, 400); 
  },
 fetchTickets(page = 1) {
  this.loading = true;
  const params = { page };

  if (this.filters.dateFrom) params.dateFrom = this.filters.dateFrom;
  if (this.filters.dateTo) params.dateTo = this.filters.dateTo;

  if (this.filters.engineer) {
    if (Array.isArray(this.filters.engineer)) {
      params.engineer = this.filters.engineer.map(e => e.engineer); 
    } else {
      params.engineer = this.filters.engineer.engineer || this.filters.engineer;
    }
  }

  if (this.globalSearch) params.search = this.globalSearch;

  axios.get('/api/tickets', { params })
    .then(res => {
      this.tickets = res.data.data;
      this.pagination = {
        current_page: res.data.current_page,
        last_page: res.data.last_page,
        per_page: res.data.per_page,
        total: res.data.total,
      };
    })
    .catch(err => console.error(err))
    .finally(() => {
      this.loading = false;
    });
},

  handleFilter() {
    console.log("Filter button clicked ✅", this.filters);
    this.fetchTickets(1);
  },
  changePage(page) {
    if (page >= 1 && page <= this.pagination.last_page) {
      this.fetchTickets(page);
    }
  },
  alignClass(side) {
    return side === 'center' ? 'text-center' : side === 'right' ? 'text-right' : 'text-left';
  },
  severityClass(value) {
    if (!value) return '';
    const v = String(value).toLowerCase();

    if (v === 'high') return 'severity-red';
    if (v === 'critical') return 'severity-orange';
    if (v === 'medium') return 'severity-yellow';
    return 'severity-green';
  },
  formatDate(val) {
    if (!val) return '';
    const d = new Date(val);
    return d.toLocaleDateString('en-US');
  },
  fetchEngineers() {
        axios.get('/api/fetch-engineers')
          .then(res => {
            this.filteredEngineers = res.data.map(user => ({
              engineer: user.name,
            }));
          })
          .catch(err => console.error('Failed to fetch engineers:', err));
      },
      closeDropdown() {
        setTimeout(() => {
          this.isDropdownOpen = false;
        }, 150);
      },
        openModal(ticket) {
        this.selectedTicket = ticket; 
        this.isModalOpen = true;
        document.body.style.overflow = 'auto';
      },
      closeModal() {
        this.selectedTicket = null;
        this.isModalOpen = false;
        document.body.style.overflow = '';
      },
     shortenName(filename) {
      if (!filename || typeof filename !== "string") {
        return "Invalid filename"; // or just return ""
      }
      return filename.length > 30
        ? filename.substring(0, 16) + "..."
        : filename;
    },

      downloadFile(filename) {
      const url = `${window.location.origin}/api/attachments/download/${filename}`;
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", filename);
      link.setAttribute("target", "_blank"); // optional
      link.setAttribute("rel", "noopener"); // optional
      link.click();
    },

     canEditTicket(ticket) {
      if (this.authUser.role === 'admin' || this.authUser.role === '1') {
        return true;
      }

      const assignments = ticket.assignments || [];

      const isAssigned = assignments.some(a =>
        this.authUser.name === a.assign_name ||
        this.authUser.email === a.email
      );

      return isAssigned;
    },

    editTicket(ticket) {
        if (!ticket || !ticket.ticket_id) {
          console.error("Ticket has no ticket_id:", ticket);
          return;
        }

        this.$router.push({ name: 'EditTicket', params: { id: ticket.ticket_id } }).then(() => {
          this.$nextTick(() => {
            document.body.style.overflow = 'auto';
          });
        });
      },
      resolveTicket(ticket) {
        if (!ticket || !ticket.ticket_id) {
          console.error("Ticket has no ticket_id:", ticket);
          return;
        }

        this.$router.push({ name: 'ResolveClientTicket', params: { id: ticket.ticket_id } }).then(() => {
          this.$nextTick(() => {
            document.body.style.overflow = 'auto';
          });
        });
      }

},
mounted() {
  this.fetchTickets();
  this.fetchEngineers();
  axios.get('/api/user')
    .then(res => {
      this.authUser = res.data;
    })
    .catch(err => console.error(err));
},
}
</script>

