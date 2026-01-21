import axios from "axios";
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import UpdateModal from './pages/UpdateModal.vue';
import Swal from 'sweetalert2'
import dayjs from 'dayjs'



export default {
  name: 'EditTicket',
  components: { Multiselect, UpdateModal,  },
  props: ['id'],
  
  data() {
    return {
      currentYear: new Date().getFullYear(),
        ticketDetailsFields: [
          { name: 'name', label: 'Name', type: 'text', model: '', required: true },
          { name: 'company_name', label: 'Company Name', type: 'text', model: '',required: true },
          { name: 'address', label: 'Address', type: 'text', model: '', required: true },
          { name: 'contact_email', label: 'Email', type: 'email', model: '', required: true},
          { name: 'severity', label: 'Severity', type: 'select', model: '', required: true, options: [
            { name: 'Low', value: 'Low' },
            { name: 'Medium', value: 'Medium' },
            { name: 'High', value: 'High' },
            { name: 'Critical', value: 'Critical' },
          ], },
        ],

        contactFields: [
          { name: 'contact_number', label: 'Phone Number', type: 'number', model: '' , required: true},
          { name: 'productName', label: 'Choose Product', type: 'select', model: '', hidden: false, options: [], required: true},
          { name: 'Reseller_name', label: 'Reseller Name', type: 'text', model: '', hidden: true },
          { name: 'serial_number', label: 'Serial Number', type: 'text', model: '', hidden: true },
          { name: 'License', label: 'License/Activation Code', type: 'text', model: '', hidden: true },
          { name: 'partner_name', label: 'Service Partner', type: 'text', model: '', hidden: true },
         { name: 'sapdatabase_name', 
            label: 'SAP B1 Database', 
            type: 'select', 
            model:  '' , 
            options: [
                { name: 'MS SQL', value: 'MS SQL' },
                { name: 'HANA', value: 'HANA' },
            ], 
           
            hidden: true
            },
          { name: 'infrastructure', label: 'Infrastructure', type: 'select', model: '',options: [
            { name: 'On-premise', value: 'On-premise' },
            { name: 'Go-Qloud with SAP B1', value: 'Go-Qloud with SAP B1' },
          ], hidden: true },
          { name: 'sap_version', label: 'SAP B1 Version', type: 'select', model: '',options: [], hidden: true},
          { name: 'assignEngineer', label: 'Assign to Engineer', type: 'select', model: '', options: [], hidden: true },

        ],
        concernField:[
          { name: 'attachment', label: 'Attachment (optional)', type: 'file', model: '', hidden: false },
          { name: 'concern', label: 'Concern/Issue', type: 'textarea', model: '', hidden: false, required: true },
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
      const productField = this.contactFields.find(f => f.name === 'productName');
      
      const product = productField?.model?.name || '';

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

        const generalFields = ['company_name','contact_name','contact_email','contact_number','concern','productName'];
        if (generalFields.includes(f.name)) return true;

        return false;
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

  async fetchProducts() {
        try {
          const res = await axios.get('/api/products');
          const productField = this.contactFields.find(f => f.name === 'productName');

          if (productField) {
            productField.options = res.data.map(p => ({
              name: p.prod_name,
              value: p.prod_id
            }));
          } else {
            console.warn('Product field not found in contactFields');
          }
        } catch (error) {
          console.error('Failed to fetch products:', error);
        }
      },

      onSelectChange(field) {
        console.log('Selected:', field.model);
      },

    async addTicket() {
      const allFields = [
        ...this.ticketDetailsFields,
        ...this.contactFields,
        ...this.concernField,
      ];
      for (const field of allFields) {
        if (field.required && (!field.model || field.model === '')) {
          await Swal.fire({
            icon: 'warning',
            title: 'Required Field Missing',
            text: `Please fill in "${field.label}" before submitting.`,
          });
          return;
        }
      }

      for (const field of allFields) {
        if (field.type === 'file' && field.model instanceof File) {
          if (field.model.size > 1024 * 1024) {
            await Swal.fire({
              icon: 'error',
              title: 'File Too Large',
              text: `The file "${field.model.name}" exceeds 1MB. Please upload a smaller file.`,
            });
            return;
          }
        }
      }

      try {
        Swal.fire({
          title: 'Processing...',
          text: 'Submitting your ticket, please wait.',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });

        const formData = new FormData();
        allFields.forEach((field) => {
          let value = field.model;

        if (typeof value === 'object' && value !== null) {
          if (field.name === 'assignEngineer') {
            formData.append('assignEngineerName', value.value.name);  
            formData.append('assignEngineerEmail', value.value.email);
            value = value.value.name;  
          } else {
            value = value.value || value.name;
          }
        }

          if (field.type === 'file' && field.model instanceof File) {
            formData.append(field.name, field.model);
          } else {
            formData.append(field.name, value ?? '');
          }
        });

        const res = await axios.post('/api/clientAddTickets', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        Swal.close();
        await Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: res.data.message || 'Your ticket has been submitted successfully.',
        });

        this.ticketDetailsFields.forEach(f => f.model = '');
        this.contactFields.forEach(f => f.model = '');
        this.concernField.forEach(f => {
          if (f.type === 'file') {
            f.model = null;
            f.selectedFileName = '';

            const fileInput = document.querySelector(
              `input[type="file"][name="${f.name}"]`
            );
            if (fileInput) fileInput.value = '';
          } else {
            f.model = '';
          }
        });


      } catch (err) {
        Swal.close();
        console.error('Error:', err);

        if (err.response && err.response.status === 422) {
          const errors = err.response.data.errors;
          const messages = errors
            ? Object.values(errors).flat().join('\n')
            : err.response.data.message || 'Validation failed';
          Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: messages,
          });
        } else if (err.response && err.response.status === 403) {
          // Swal.fire({
          //   icon: 'error',
          //   title: 'reCAPTCHA Failed',
          //   text: 'Please refresh the page and try again.',
          // });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message || 'Something went wrong while saving.',
          });
        }
      }
    },


 handleFileUpload(event, field) {
    const file = event.target.files[0];
    if (!file) {
      field.selectedFileName = '';
      field.model = null;
      return;
    }

    const maxSize = 1 * 1024 * 1024;
    if (file.size > maxSize) {
      Swal.fire({
        icon: 'error',
        title: 'File too large',
        text: 'The file size must not exceed 1 MB.',
      });
      event.target.value = ''; 
      field.selectedFileName = '';
      field.model = null;
      return;
    }

    field.selectedFileName = file.name;
    field.model = file;
  },


},
mounted() {
 axios.get('/api/fetch-engineers').then(res => {
  const engineerField = this.contactFields.find(f => f.name === 'assignEngineer');
  if (engineerField) {
    engineerField.options = res.data.map(u => ({
      name: u.name,
      value: { name: u.name, email: u.email },
    }));
  }
});
  axios.get('/api/tickets/sap-versions')
    .then(res => {
      const sapVersionField = this.contactFields.find(f => f.name === 'sap_version');
      if (sapVersionField) {
        sapVersionField.options = res.data.map(v => ({
          name: v.trim(),  
          value: v.trim()
        }));
      }
      console.log('Loaded SAP Versions:', sapVersionField.options);
    })
    .catch(err => {
      console.error('Failed to load SAP versions:', err);
    });
  this.fetchProducts();
  },
};
