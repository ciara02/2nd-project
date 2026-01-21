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
        <div class="flex flex-wrap -mx-3" :style="{ marginTop: '0' }">
          <div class="w-full max-w-2xl px-3 mx-auto">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl rounded-2xl">
              <!-- Ticket Details Section -->
                <div class="flex-auto p-6">
                  <h6 class="mb-0 text-lg font-semibold">Ticket Details</h6>
                    <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                       <div class="flex justify-center">
                        <div class="w-full md:w-10/12 px-3 mb-6">
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                            <template v-for="field in ticketDetailsFields.filter(f => f.name !== 'solution')" :key="field.name">
                            <div v-if="loading" class="loading-mask">
                            </div>
                              <div class="grid grid-cols-[160px_1fr] items-center gap-x-4">
                                <label
                                  :for="field.name"
                                  class="font-semibold text-sm text-slate-700 dark:text-white/80 mt-4"
                                >
                                  {{ field.label }}
                                </label>

                                <div class="w-full">
                                  <!-- handle field types -->
                                  <multiselect
                                    v-if="field.type === 'select'"
                                    v-model="field.model"
                                    :options="field.options"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :placeholder="`Select Engineers`"
                                    label="name"
                                    track-by="name"
                                    class="w-full text-sm consistent-multiselect"
                                  ></multiselect>

                                  <input
                                    v-else
                                    :type="field.type"
                                    v-model="field.model"
                                    :id="field.name"
                                    :disabled="field.disabled"
                                    :placeholder="`${field.placeholder}`"
                                    class="border rounded px-2 py-1 w-full"
                                  />
                                </div>
                              </div>
                            </template>

                            <!-- Solution textarea spans full width -->
                            <div class="md:col-span-2 grid grid-cols-[160px_1fr] items-start gap-x-4 mt-4">
                              <label
                                for="solution"
                                class="font-semibold text-sm text-slate-700 dark:text-white/80"
                              >
                                Solution
                              </label>

                              <textarea
                                id="solution"
                                v-model="ticketDetailsFields.find(f => f.name === 'solution').model"
                                class="border rounded px-2 py-1 w-full resize-y"
                                rows="4"
                                placeholder="Enter solution details"
                              ></textarea>
                            </div>
                          </div>
                        </div>
                      </div>

                      
                <!-- Save / Cancel Buttons -->
                <div class="flex justify-end gap-2 mt-4">
                  <button @click="$router.push('/pendingtickets')" class="px-4 py-2 btn-gradient-red rounded">
                    Cancel
                  </button>
                  <button  @click="saveResolvedData" class="px-4 py-2 btn-gradient-green text-white rounded">
                   Resolve Ticket
                  </button>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
  </main>
  </body>
</template>

<script>
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import Footer from './Footer.vue';
import axios from "axios";
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import UpdateModal from './UpdateModal.vue';
import Swal from 'sweetalert2'
import dayjs from 'dayjs'

export default {
  name: 'EditTicket',
  components: { Sidebar, Navbar, Footer, Multiselect, UpdateModal },
   props: ['id'],

  data() {
    return {
      currentYear: new Date().getFullYear(),
        ticketDetailsFields: [
          { name: 'company_name', label: 'Company Name', type: 'text', model: '', disabled: true, placeholder: 'Company Name' },
          { name: 'contact_name', label: 'Contact Name', type: 'text', model: '',disabled: true, placeholder: 'Contact Name' },
          { name: 'contact_email', label: 'Email', type: 'email', model: '',disabled: true, placeholder: 'Email' },
          { name: 'assign_name', label: 'Assigned Engineer', type: 'text', model: '', disabled: true, placeholder: 'Engineer Name' },
          { name: 'assignEngineer', label: 'Resolved By', type: 'select', model: '', options: [] },  
          { name: 'solution', label: 'Solution', type: 'textarea', model: '', hidden: false, placeholder: 'Write the solution' },
        ],
        selectedEngineer: null,
        engineerOptions: [],
        loading: false,

  };
  },
  methods: {
    saveResolvedData() {
      const assignEngineerField = this.ticketDetailsFields.find(f => f.name === 'assignEngineer');
      const solutionField = this.ticketDetailsFields.find(f => f.name === 'solution');

      const payload = {
        ticket_id: this.id,
        assignEngineer: assignEngineerField?.model?.name || '',
        solution: solutionField?.model || '',
      };

      if (!payload.assignEngineer) {
          Swal.fire({
            icon: 'warning',
            title: 'Missing Field',
            text: 'Please select an engineer before saving.',
          });
          return;
        }
        if (!payload.solution) {
          Swal.fire({
            icon: 'warning',
            title: 'Missing Field',
            text: 'Please enter a solution before saving.',
          });
          return;
        }
      Swal.fire({
        title: 'Mark as Resolved?',
        text: 'Are you sure you want to mark this ticket as resolved?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resolve it',
        cancelButtonText: 'Cancel',
      }).then(result => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Processing...',
            text: 'Email will be sent to the requester regarding the resolution.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          axios.post('/api/tickets/saveSolution', payload)
            .then(res => {
              Swal.close();

              Swal.fire({
                icon: 'success',
                title: 'Ticket Resolved!',
                text: 'Email will be sent to the requester regarding the resolution.',
                timer: 1800,
                showConfirmButton: false,
              }).then(() => {
                this.$router.push({ name: 'PendingTickets' });
              });
            })
            .catch(err => {
              console.error(err);
              Swal.close(); 
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong while saving the solution.',
              });
            });
        }
      });
    },
  },

async mounted() {
  try {
    Swal.fire({
      title: 'Processing...',
      text: 'Loading ticket details. Please wait.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const engineerRes = await axios.get('/api/fetch-engineers');
    const engineerField = this.ticketDetailsFields.find(f => f.name === 'assignEngineer');
    if (engineerField) {
      engineerField.options = engineerRes.data.map(u => ({
        name: u.name,
        email: u.email,
      }));
    }

    const ticketRes = await axios.get(`/api/tickets/resolveTicket/${this.id}`);
    const { ticket, references, engineers } = ticketRes.data;

    this.ticketDetailsFields.forEach(f => {
      f.model = ticket[f.name] || '';
    });

    if (ticket.assignments && ticket.assignments.length > 0) {
      const engineerNames = ticket.assignments.map(a => a.assign_name);
      const combinedNames = engineerNames.join(', ');
      const assignNameField = this.ticketDetailsFields.find(f => f.name === 'assign_name');
      if (assignNameField) {
        assignNameField.model = combinedNames;
      }
    }
    Swal.close();
  } catch (error) {
    console.error("Error fetching ticket:", error);

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to load ticket details. Please try again later.',
    });
  }
}

}
</script>
