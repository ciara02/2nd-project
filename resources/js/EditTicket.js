import Sidebar from './pages/Sidebar.vue';
import Navbar from './pages/Navbar.vue';
import Footer from './pages/Footer.vue';
import axios from "axios";
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import UpdateModal from './pages/UpdateModal.vue';
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
          { name: 'company_name', label: 'Company Name', type: 'text', model: '', disabled: true },
          { name: 'contact_name', label: 'Contact Name', type: 'text', model: '',disabled: true},
          { name: 'contact_email', label: 'Email', type: 'email', model: '', },
          { name: 'contact_number', label: 'Phone Number', type: 'text', model: '', disabled: true},
          { name: 'ticketRef', label: 'Ticket Reference', type: 'select', model: '', options: []},
        ],

        contactFields: [
          { name: 'concern', label: 'Concern/Issue', type: 'textarea', model: '', hidden: false },
          { name: 'productName', label: 'Product Name', type: 'text', model: '', disabled: true, hidden: false },
          { name: 'Reseller_name', label: 'Reseller Name', type: 'text', model: '', disabled: true, hidden: true },
          { name: 'serial_number', label: 'Serial Number', type: 'text', model: '', disabled: true, hidden: true },
          { name: 'License', label: 'License/Activation Code', type: 'text', model: '', disabled: true, hidden: true },
          { name: 'partner_name', label: 'Service Partner', type: 'text', model: '', disabled: true, hidden: true },
         { name: 'sapdatabase_name', 
            label: 'SAP B1 Database', 
            type: 'select', 
            model: { name: 'MS SQL', value: 'MS SQL' }, 
            options: [
                { name: 'MS SQL', value: 'MS SQL' },
                { name: 'HANA', value: 'HANA' },
            ], 
            disabled: true, 
            hidden: true
            },
          { name: 'infrastructure', label: 'Infrastructure', type: 'select', model: { name: 'On-premise', value: 'On-premise' },options: [
            { name: 'On-premise', value: 'On-premise' },
            { name: 'Go-Qloud with SAP B1', value: 'Go-Qloud with SAP B1' },
          ], disabled: true, hidden: true },
          { name: 'sap_version', label: 'SAP B1 Version', type: 'select', model: '',options: [], disabled: true, hidden: true},
          { name: 'assignEngineer', label: 'Assign to Engineer', type: 'select', model: '', options: [], hidden: true },

        ],
        selectedEngineer: null,
        engineerOptions: [],
        activityLogs: [],
        statusLogs: [],
        assignmentHistory: [],
        tablesLoading: true,
        showStatusModal: false,
        showRemarksModal: false,
        showSeverityModal: false,
        newStatus: '',
        customStatusText: '',
        newRemarks: '',
        newSeverity: '',
        records: [],
      
    };
  },
  computed: {
     visibleFields() {
        const product = (this.contactFields.find(f => f.name === 'productName')?.model || '').trim();

        const productFieldMap = {
            SAP: ['partner_name','sapdatabase_name','infrastructure','sap_version'],
            Microsoft: ['Reseller_name','assignEngineer'],
            'HP Aruba': ['Reseller_name','serial_number','License','assignEngineer'],
            'Huawei IT/IP': ['Reseller_name','serial_number','License','assignEngineer'],
            'Hitachi Vantara': ['Reseller_name','serial_number','License','assignEngineer'],
            Fireeye: ['Reseller_name','serial_number','License','assignEngineer'],
            Fortinet: ['Reseller_name','serial_number','License','assignEngineer'],
            'Trend Micro': ['Reseller_name','serial_number','License','assignEngineer'],
            Cisco: ['Reseller_name','serial_number','License','assignEngineer'],
            Lenovo: ['Reseller_name','serial_number','License','assignEngineer'],
        };

        return this.contactFields.filter(f => {
            const allowedFields = productFieldMap[product] || [];
            if (allowedFields.includes(f.name)) return true;

            const generalFields = ['company_name','contact_name','contact_email','contact_number','ticketRef','concern','productName'];
            if (generalFields.includes(f.name)) return true;

            return false;
        });
        }
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
      const engineerField = this.contactFields.find(f => f.name === 'assignEngineer');
      if (engineerField) {
        engineerField.options = engineerRes.data.map(u => ({
          name: u.name,
          email: u.email
        }));
      }

      // === Fetch ticket details ===
      const ticketRes = await axios.get(`/api/tickets/edit/${this.id}`);
      const { ticket, references, engineers } = ticketRes.data;

      // Populate models
      this.ticketDetailsFields.forEach(f => f.model = ticket[f.name] || '');
      this.contactFields.forEach(f => f.model = ticket[f.name] || '');

      // Ticket reference field setup
      const ticketRefField = this.ticketDetailsFields.find(f => f.name === 'ticketRef');
      if (ticketRefField) {
        ticketRefField.options = references.map(r => ({ name: r, value: r }));
        ticketRefField.model = { name: ticket.ticketRef, value: ticket.ticketRef };
      }

      // Product name handling
      this.contactFields.forEach(f => {
        if (f.name === 'productName') {
          f.model = ticket.product_line?.prod_name || '';
        } else {
          f.model = ticket[f.name] || '';
        }
      });

      // Logs
      this.activityLogs = ticket.remarks_logs || [];
      this.statusLogs = ticket.update_status || [];
      this.assignmentHistory = ticket.assignments || [];
      this.tablesLoading = false;

      const sapVersionRes = await axios.get('/api/tickets/sap-versions');
      const sapVersionField = this.contactFields.find(f => f.name === 'sap_version');
      if (sapVersionField) {
        sapVersionField.options = sapVersionRes.data.map(v => ({
          name: v.trim(),
          value: v.trim()
        }));
        console.log('Loaded SAP Versions:', sapVersionField.options);
      }

      Swal.close();

    } catch (error) {
      console.error('Error loading ticket data:', error);

      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to load ticket details. Please try again later.',
      });
    }
  },
 methods: {
     formatDate(date) {
      if (!date) return '';
      return dayjs(date).format('MM/DD/YYYY hh:mm A');
    },
    numericOnly(event) {
        const key = event.key;
        if (!/[\d]/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight') {
        event.preventDefault();
        }
    },
    async handleStatusSave() {
      const status =
        this.newStatus === 'Custom' ? this.customStatusText : this.newStatus

      this.showStatusModal = false

      if (!status) {
        await Swal.fire({
          icon: 'warning',
          title: 'Missing Status',
          text: 'Please select or enter a status before saving.',
        })
        this.showStatusModal = true
        return
      }

      const result = await Swal.fire({
        title: 'Confirm Status Update',
        text: `Are you sure you want to set the status to "${status}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it',
        cancelButtonText: 'Cancel',
      })

      if (!result.isConfirmed) {
        this.showStatusModal = true
        return
      }

      try {
       const response = await axios.post('/api/tickets/status', {
            ticket_id: this.id,
            status: this.newStatus,
            custom_status: this.customStatusText,
        });


        await Swal.fire({
          icon: 'success',
          title: 'Status Updated',
          text: 'The new status has been saved successfully!',
          timer: 1800,
          showConfirmButton: false,
        })

        this.statusLogs.push(response.data.newStatusRecord);

        this.showStatusModal = false
        this.newStatus = ''
        this.customStatusText = ''
      } catch (error) {
        console.log(error); 
        await Swal.fire({
          icon: 'error',
          title: 'Save Failed',
          text: error.response?.data?.message || 'Something went wrong.',
        })

        this.showStatusModal = true
      }
    },

    async handleRemarksSave() {
        const remarks = this.newRemarks === 'Custom' ? this.customRemarksText : this.newRemarks;

        this.showRemarksModal = false;

        if (!remarks) {
            Swal.fire({
            icon: 'warning',
            title: 'Missing Remarks',
            text: 'Please enter remarks before saving.',
            });
            this.showRemarksModal = true;
            return;
        }

        const result = await Swal.fire({
            title: 'Confirm Remarks',
            text: 'Do you want to add these remarks?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it',
            cancelButtonText: 'Cancel',
        });

        if (!result.isConfirmed) {
            this.showRemarksModal = true;
            return;
        }

        try {
            const response = await axios.post('/api/tickets/remarks', {
            ticket_id: this.id,
            logs: remarks,
            });

            if (response.data.newRemarkRecord) {
            this.activityLogs.push(response.data.newRemarkRecord);
            }

            Swal.fire({
            icon: 'success',
            title: 'Remarks Added',
            text: response.data.message,
            timer: 1800,
            showConfirmButton: false,
            });

            this.newRemarks = '';
            this.customRemarksText = '';
            this.showRemarksModal = false;

        } catch (error) {
            console.log(error);
            Swal.fire({
            icon: 'error',
            title: 'Save Failed',
            text: error.response?.data?.message || 'Something went wrong.',
            });
            this.showRemarksModal = true;
        }
    },

    async handleSeveritySave() {
    const severity = this.newSeverity === 'Custom' ? this.customSeverityText : this.newSeverity;

    this.showSeverityModal = false;

    if (!severity) {
        Swal.fire({
        icon: 'warning',
        title: 'Missing Severity Level',
        text: 'Please choose a severity level before saving.',
        });
        this.showSeverityModal = true;
        return;
    }

    const result = await Swal.fire({
        title: 'Confirm Severity Update',
        text: `Change severity to "${severity}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it',
        cancelButtonText: 'Cancel',
    });

    if (!result.isConfirmed) {
        this.showSeverityModal = true;
        return;
    }

    try {
        const response = await axios.put('/api/tickets/severity', {
        ticket_id: this.id,
        severity: severity,
        severity_reason: this.newSeverityRemarks,
        });

        // Push or update severity record in frontend (optional)
        if (response.data.updatedSeverityRecord) {
        this.ticketDetailsFields.forEach(f => {
            if (f.name === 'severity') f.model = response.data.updatedSeverityRecord.severity;
        });
        }

        Swal.fire({
        icon: 'success',
        title: 'Severity Updated',
        text: response.data.message,
        timer: 1800,
        showConfirmButton: false,
        });

        this.newSeverity = '';
        this.newSeverityRemarks = '';
        this.showSeverityModal = false;

    } catch (error) {
        console.log(error);
        Swal.fire({
        icon: 'error',
        title: 'Update Failed',
        text: error.response?.data?.message || 'Something went wrong.',
        });
        this.showSeverityModal = true;
    }
    },
async updateTicket() {
  try {
    Swal.fire({
      title: 'Processing...',
      text: 'Updating ticket, please wait.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const ticketPayload = {};
    [...this.ticketDetailsFields, ...this.contactFields].forEach(f => {
      if (typeof f.model === 'object' && f.model !== null) {
        ticketPayload[f.name] = f.model.value || f.model.name;
      } else {
        ticketPayload[f.name] = f.model;
      }
    });

    const engineerField = this.contactFields.find(f => f.name === 'assignEngineer');
    if (engineerField && engineerField.model) {
      ticketPayload.engineer_name = engineerField.model.name;
      ticketPayload.engineer_email = engineerField.model.email;
    }

    const response = await axios.post(`/api/tickets/update/${this.id}`, ticketPayload, { withCredentials: true });

    Swal.close();

    if (response.data.assignedEngineer) {
      if (response.data.success) {
        this.assignmentHistory.push(response.data.assignedEngineer);

        await Swal.fire({
          icon: 'success',
          title: 'Ticket Updated',
           text: 'Email will be sent to the assigned engineer regarding the update.',
          timer: 1500,
          showConfirmButton: false,
        });

        this.$router
          ? this.$router.push('/pendingtickets')
          : (window.location.href = '/pendingtickets');
      } else {
        Swal.fire({ icon: 'warning', title: 'Notice', text: response.data.message });
      }
    } else {
      await Swal.fire({
        icon: 'success',
        title: 'Ticket Updated',
        text: 'Email will be sent to the assigned engineer regarding the update.',
        timer: 1500,
        showConfirmButton: false,
      });

      this.$router
        ? this.$router.push('/pendingtickets')
        : (window.location.href = '/pendingtickets');
    }

    this.assignEngineer = '';

  } catch (error) {
    console.error(error);
    Swal.close(); 
    Swal.fire({
      icon: 'error',
      title: 'Update Failed',
      text: error.response?.data?.message || 'Something went wrong.',
    });
  }
}
},
};
