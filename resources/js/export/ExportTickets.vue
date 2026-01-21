<template>
  <div>
    <!-- Header + Buttons -->
    <div class="flex items-center justify-between mb-1">

     <div class="flex gap-2">
        <button 
            class="btn-gradient-green flex items-center gap-2" 
            @click="exportExcel" 
            :disabled="loading" 
            :class="{'opacity-50': loading}">
            <i class="fa fa-download text-white mr-2"></i>
            <span v-if="loading">Excel</span>
            <span v-else>Excel</span>
        </button>

        <button 
            class="btn-gradient-slate flex items-center gap-2" 
            @click="printTickets" 
            :disabled="loading" 
            :class="{'opacity-50': loading}">
            <i class="fa fa-print text-white mr-2"></i>
            <span v-if="loading">Print</span>
            <span v-else>Print</span>
        </button>
    </div>
    </div>
  </div>
</template>

<script>
import ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import Swal from 'sweetalert2';
import axios from "axios";

export default {
  name: "ExportTickets",
    data() {
      return {
          loading: false, 
      };
  },

  props: {
    tickets: { type: Array, required: true },
    filters: { type: Object, required: true },

    title: { type: String, required: true },
    headers: { type: Array, required: true },
    columns: { type: Array, required: true } 
  },

  methods: {
    formatDate(value) {
      if (!value) return "N/A";

      const date = new Date(value);
      const options = {
        month: "2-digit",
        day: "2-digit",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
      };

      return date.toLocaleString("en-US", options);
    },

    async exportExcel() {
      this.loading = true;
      try {
        const response = await axios.get("/api/tickets/export", {
        params: this.filters,
      });
      const data = response.data.data;

       if (!data || data.length === 0) {
         Swal.fire({
              icon: 'info',
              title: 'No tickets found for exporting.',
              text: 'Please try with different filters.',
              confirmButtonText: 'Okay'
          });
          return;
      }

      const workbook = new ExcelJS.Workbook();
      const worksheet = workbook.addWorksheet("Tickets");

      worksheet.mergeCells(1, 1, 1, this.headers.length);
      const titleCell = worksheet.getCell("A1");
      titleCell.value = this.title;
      titleCell.font = { bold: true, size: 16 };
      titleCell.alignment = { horizontal: "center" };

      const headerRow = worksheet.addRow(this.headers);
      headerRow.eachCell((cell) => {
        cell.font = { bold: true };
        cell.alignment = { horizontal: "center" };
      });

      data.forEach(ticket => {
        const rowData = this.columns.map(col => {
          const value = col.map(ticket) || "N/A";

          if (col.key === "date_created") {
            return this.formatDate(value);
          }

          return value;
        });

        worksheet.addRow(rowData);
      });

      worksheet.columns.forEach((col) => {
        let maxLength = 0;

        col.eachCell({ includeEmpty: true }, (cell) => {
          const cellValue = cell.value ? cell.value.toString() : "";
          maxLength = Math.max(maxLength, cellValue.length);
        });

        col.width = maxLength < 15 ? 15 : maxLength + 2;
      });

      const buffer = await workbook.xlsx.writeBuffer();
      saveAs(new Blob([buffer]), `${this.title}.xlsx`);
        } catch (error) {
            console.error("Error exporting tickets:", error);
        } finally {
            this.loading = false;  
        }

    },

    async printTickets() {
      this.loading = true;
      try {
              const response = await axios.get("/api/tickets/export", {
        params: this.filters,
      });
      const allTickets = response.data.data;

       if (!allTickets || allTickets.length === 0) {
         Swal.fire({
              icon: 'info',
              title: 'No tickets found for printing.',
              text: 'Please try with different filters.',
              confirmButtonText: 'Okay'
          });
          return;
      }

      const rowsHtml = allTickets.map(ticket => `
        <tr>
          ${this.columns.map(col => {
            let value = col.map(ticket) || "N/A";

            if (col.key === "date_created") {
              value = this.formatDate(value);
            }

            return `<td>${value}</td>`;
          }).join("")}
        </tr>
      `).join("");

      const popup = window.open("", "_blank", "width=1000,height=700");

      popup.document.write(`
        <html>
          <head>
            <title>${this.title}</title>
            <style>
              table { width: 100%; border-collapse: collapse; font-size: 14px; }
              th { background: #4CAF50; padding: 8px; }
              td { padding: 6px; border-bottom: 1px solid #ccc; }
              h2 { text-align: center; }
            </style>
          </head>
          <body>
            <h2>${this.title}</h2>
            <table>
              <thead>
                <tr>
                  ${this.headers.map(h => `<th>${h}</th>`).join("")}
                </tr>
              </thead>
              <tbody>
                ${rowsHtml}
              </tbody>
            </table>
          </body>
        </html>
      `);

      popup.document.close();
      popup.print();
      } catch (error) {
            console.error("Error printing tickets:", error);
        } finally {
            this.loading = false;  
        }
    }
  }
};
</script>


<style scoped>
@media print {
  #print-area {
    display: block;
  }
}
</style>
