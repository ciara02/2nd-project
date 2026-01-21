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

                <!-- Product Line -->
                <div class="flex-1 px-3">
                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                    Product Line
                </label>
                <Multiselect
                    v-model="filters.productLine"
                    :options="productLines"
                    placeholder="Select product line"
                    track-by="value"
                    label="label"
                    class="consistent-multiselect"
                />
                </div>

                <!-- Company -->
                <div class="flex-1 px-3">
                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                    Company
                </label>
               <Multiselect
                    v-model="filters.company"
                    :options="companies"
                    placeholder="Select company"
                    track-by="value"
                    label="label"
                    class="consistent-multiselect"
                />
                </div>

                <!-- Severity -->
                <div class="flex-1 px-3">
                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                    Severity
                </label>
                <Multiselect
                    v-model="filters.severity"
                    :options="severities"
                    placeholder="Select severity"
                    track-by="value"
                    label="label"
                    class="consistent-multiselect"
                    />
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
                    <h6 class="mb-0 dark:text-white">Ticket Request View</h6> 
                  </div> 
                  <div class="flex flex-wrap -mx-3"> 
                    <div class="flex items-center flex-none w-1/2 max-w-full px-3"> 
                     <ExportRequestedTickets
                        :tickets="tickets"
                        :filters="filters"
                        title="iSupport Ticketing System - Requested Tickets"
                        :headers="[
                          'Date','Ticket #','Severity','Engineer','Product Line',
                          'License Code','Reseller Name','Serial #','Service Partner',
                          'SAP b1 Database','Infrastructure','SAP b1 Version','Company Name',
                          'Address','Concern'
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
                          { key: 'address', map: (ticket) => ticket.address || 'N/A' },
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
                      <p class="mt-2"><span class="font-semibold">Address:</span> {{ selectedTicket?.address || 'N/A' }}</p>
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
                    <template #footer v-if="userRole === '1'">
                      <button
                        class="btn-gradient-green"
                        @click="confirmAction('approve', selectedTicket)"
                      >
                        Approve
                      </button>

                      <button
                        class="btn-gradient-red"
                        @click="confirmAction('decline', selectedTicket)"
                      >
                        Decline
                      </button>
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
import Swal from 'sweetalert2';
import ExportRequestedTickets from "../export/ExportTicketsRequest.vue";
import axios from "axios";

export default {
  name: "App",
  components: { Sidebar, Navbar, Footer, BaseModal, Multiselect, ExportRequestedTickets },
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
        productLine: null,  
        company: null,
        severity: null,
    },
       pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      },
      isModalOpen: false,
      selectedTicket: null,
      loading: false,
      baseUrl: '',
      globalSearch: '',
      debounceTimer: null,
      authUser: null,
      productLines: [],  // ✅ make sure these exist and are arrays
      companies: [],
      severities: [],
      userRole: '',
    };
  },
    watch: {
    'filters.productLine'(newVal) {
        this.fetchTickets(1)
    },
    'filters.company'(newVal) {
        this.fetchTickets(1)
    },
    'filters.severity'(newVal) {
        this.fetchTickets(1)
    }
    },
computed: {
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
     fetchFiltersData() {
        axios.get('/api/filters/data')
        .then(res => {
            const productLines = res.data?.productLines || [];
            const companies = res.data?.companies || [];

            this.productLines = productLines.map(p => ({
            label: p.prod_name,
            value: p.prod_id
            }));

            this.companies = companies.map(c => ({
            label: c.company_name,
            value: c.company_name
            }));

            this.severities = [
                { label: 'Low', value: 'Low' },
                { label: 'Medium', value: 'Medium' },
                { label: 'High', value: 'High' },
                { label: 'Critical', value: 'Critical' }
            ]
        })
        .catch(err => {
            console.error('Failed to load product line/company data:', err);
        });
  },
  debouncedSearch() {
    clearTimeout(this.debounceTimer);
    this.debounceTimer = setTimeout(() => {
      this.fetchTickets(1);
    }, 400); 
  },
 fetchTickets(page = 1) {
  this.loading = true;
  const params = { page };

  if (this.filters.productLine) params.productLine = this.filters.productLine.value
  if (this.filters.company) params.company = this.filters.company.value
  if (this.filters.severity) params.severity = this.filters.severity.value

  if (this.globalSearch) params.search = this.globalSearch;
  console.log('Fetching tickets with params:', params);
  axios.get('/api/tickets/request', { params })
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
      link.setAttribute("target", "_blank"); 
      link.setAttribute("rel", "noopener");
      link.click();
    },
      confirmAction(action, ticket) {
      this.isModalOpen = false;

      if (action === 'approve') {
        Swal.fire({
          title: 'Approve Ticket?',
          text: 'Are you sure you want to approve this ticket?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, approve it',
          cancelButtonText: 'No, cancel',
          confirmButtonColor: '#10b981',
          cancelButtonColor: '#6b7280',
        }).then((result) => {
          if (result.isConfirmed) {
            this.processApproval(ticket);
          } else {
            this.isModalOpen = true;
          }
        });
      } else if (action === 'decline') {
        Swal.fire({
          title: 'Decline Ticket?',
          text: 'Please provide a reason for declining this ticket.',
          icon: 'warning',
          input: 'text',
          inputPlaceholder: 'Enter decline reason...',
          showCancelButton: true,
          confirmButtonText: 'Submit',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#6b7280',
          inputValidator: (value) => {
            if (!value) return 'Please enter a reason before submitting!';
          },
        }).then((result) => {
          if (result.isConfirmed) {
            const reason = result.value;
            this.processDecline(ticket, reason);
          } else {
            this.isModalOpen = true;
          }
        });
      }
  },

  processApproval(ticket) {
    Swal.fire({
      title: 'Processing...',
      text: 'Approving ticket, please wait.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

     axios.post(`/api/tickets/${ticket.ticket_id}/approve`)
      .then(() => {
        Swal.fire('Approved!', 'The ticket has been approved successfully.', 'success');
        this.fetchTickets?.(); 
      })
      .catch(() => {
        Swal.fire('Error', 'Something went wrong while approving the ticket.', 'error');
      });
  },

  processDecline(ticket, reason) {
    Swal.fire({
      title: 'Processing...',
      text: 'Declining ticket, please wait.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    axios.post(`/api/tickets/${ticket.ticket_id}/disapprove`, { reason })
      .then(() => {
        Swal.fire('Declined!', 'The ticket has been declined successfully.', 'success');
        this.fetchTickets?.(); 
      })
      .catch(() => {
        Swal.fire('Error', 'Something went wrong while declining the ticket.', 'error');
      });
  },
},
mounted() {
  this.fetchTickets();
  this.fetchFiltersData();
  axios.get('/api/user')
    .then(res => {
      this.userRole = res.data.role;
    })
    .catch(err => console.error(err));
},
}
</script>

