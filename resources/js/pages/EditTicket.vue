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
                  <button
                    class="px-3 py-1 text-xs btn-gradient-green2 mb-2"
                     @click="showStatusModal  = true"
                     >
                      Update Status
                    </button>
                    <div class="bg-white dark:bg-slate-850 p-4 rounded-xl shadow-md mb-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Table Section -->
                          <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-sm text-slate-700 dark:text-white">
                              RMA HISTORY
                            </h3>
                          </div>
                         <div :style="{ maxHeight: '200px', overflowY: 'auto' }">
                          <table class="custom-table">
                            <thead class="bg-gray-100 dark:bg-slate-800 sticky top-0 z-10">
                              <tr class="bg-gray-100 dark:bg-slate-800">
                                <th class="text-left p-2 border">UPDATED BY</th>
                                <th class="text-left p-2 border">STATUS</th>
                                <th class="text-left p-2 border">DATE</th>
                              </tr>
                            </thead>
                            <tbody>
                              <!-- Loading State -->
                              <tr v-if="tablesLoading">
                                <td class="p-2 border text-center" :colspan="statusLogs.some(log => log.status === 'Custom') ? 4 : 3">Loading...</td>
                              </tr>

                              <!-- No Results -->
                              <tr v-else-if="statusLogs.length === 0">
                                <td class="p-2 border text-center" :colspan="statusLogs.some(log => log.status === 'Custom') ? 4 : 3">NO RESULTS FOUND</td>
                              </tr>

                              <!-- Data Rows -->
                             <tr v-else v-for="log in statusLogs" :key="log.status_id">
                                <td class="p-2 border">{{ log.update_by }}</td>
                                <td class="p-2 border">
                                    {{ log.status === 'Custom' ? `${log.Custom_Status}` : log.status }}
                                </td>
                                <td class="p-2 border">{{ formatDate(log.update_date) }}</td>
                              </tr>
                            </tbody>
                          </table>
                          </div>
                      </div>
                    </div>
                      <div class="flex gap-6 mb-2">
                        <div class="flex-1 flex justify-start mr-4">
                          <button class="px-3 py-1 text-xs btn-gradient-green2"
                           @click="showRemarksModal  = true">Add Remarks</button>
                        </div>
                        <div class="flex-1 flex justify-start ml-4">
                          <button class="px-3 py-1 text-xs btn-gradient-green2"
                           @click="showSeverityModal  = true">Edit Severity Level</button>
                        </div>
                      </div>

                      <div class="flex  ">
                          <div class="flex-1 bg-white dark:bg-slate-850 p-4 rounded-xl shadow-md mr-4">
                            <div class="flex justify-between items-center mb-2">
                              <span class="font-semibold text-sm text-slate-700 dark:text-white">ACTIVITY LOGS HISTORY</span>
                            </div>
                            <div :style="{ maxHeight: '200px', overflowY: 'auto' }">
                              <table class="custom-table">
                                <thead class="bg-gray-100 dark:bg-slate-800 sticky top-0 z-10">
                                  <tr class="bg-gray-100 dark:bg-slate-800">
                                    <th class="text-left p-2 border">UPDATED BY</th>
                                    <th class="text-left p-2 border">REMARKS</th>
                                    <th class="text-left p-2 border">DATE</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-if="tablesLoading">
                                    <td class="p-2 border text-center" colspan="3">Loading...</td>
                                  </tr>
                                  <tr v-else-if="activityLogs.length === 0">
                                    <td class="p-2 border text-center" colspan="3">NO RESULTS FOUND</td>
                                  </tr>
                                  <tr v-else v-for="log in activityLogs" :key="log.log_id">
                                    <td class="p-2 border">{{ log.log_by }}</td>
                                    <td class="p-2 border">{{ log.logs }}</td>
                                    <td class="p-2 border">{{ formatDate(log.log_date) }}</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>

                          <!-- Right Table Card -->
                          <div class="flex-1 bg-white dark:bg-slate-850 p-4 rounded-xl shadow-md ml-4">
                            <div class="flex justify-between items-center mb-2">
                              <span class="font-semibold text-sm text-slate-700 dark:text-white">ASSIGNING OF TICKET HISTORY</span>
                            </div>
                            <div :style="{ maxHeight: '200px', overflowY: 'auto' }">
                              <table class="custom-table">
                                <thead class="bg-gray-100 dark:bg-slate-800 sticky top-0 z-10">
                                  <tr>
                                    <th class="text-left p-2 border">Engineer Assigned</th>
                                    <th class="text-left p-2 border">Assigned Date</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-if="tablesLoading">
                                    <td class="p-2 border text-center" colspan="3">Loading...</td>
                                  </tr>
                                  <tr v-else-if="assignmentHistory.length === 0">
                                    <td class="p-2 border text-center" colspan="2">NO RESULTS FOUND</td>
                                  </tr>
                                  <tr v-else v-for="assign in assignmentHistory" :key="assign.assign_id">
                                    <td class="p-2 border">{{ assign.assign_name }}</td>
                                    <td class="p-2 border">{{ formatDate (assign.assign_date) }}</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                      </div>



                 <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

                  <div class="flex flex-wrap -mx-3">
                    <!-- Left Card -->
                    <div class="w-full md:w-6/12 px-3 mb-6">
                      <div class="bg-white dark:bg-slate-850 p-4 rounded-xl shadow-md">
                        <p class="uppercase text-sm mb-4">Ticket Details</p>

                        <div v-for="field in ticketDetailsFields" :key="field.name" class="mb-4">
                          <label :for="field.name" class="text-right font-bold text-xs text-slate-700 dark:text-white/80">
                            {{ field.label }}
                          </label>

                          <textarea
                            v-if="field.type === 'textarea'"
                            v-model="field.model"
                            :id="field.name"
                            class="border rounded px-2 py-1 w-full"
                          />

                          <multiselect
                            v-else-if="field.type === 'select'"
                            v-model="field.model"
                            :options="field.options"
                            :searchable="true"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :placeholder="`Select ${field.label}`"
                            label="name"
                            track-by="name"
                            class="w-full text-sm consistent-multiselect"
                          />

                          <input
                            v-else
                            :type="field.type"
                            v-model="field.model"
                            :id="field.name"
                            :disabled="field.disabled"
                            class="border rounded px-2 py-1 w-full"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Right Card -->
                    <div class="w-full md:w-6/12 px-3 mb-6">
                      <div class="bg-white dark:bg-slate-850 p-4 rounded-xl shadow-md">
                        <p class="uppercase text-sm mb-4">Contact / Assignment</p>

                        <div v-for="field in visibleFields" :key="field.name" class="mb-4">
                          <label :for="field.name" class="text-right font-bold text-xs text-slate-700 dark:text-white/80">
                            {{ field.label }}
                          </label>

                          <textarea
                            v-if="field.type === 'textarea'"
                            v-model="field.model"
                            :id="field.name"
                            class="border rounded px-2 py-1 w-full"
                          />

                          <multiselect
                            v-else-if="field.type === 'select'"
                            v-model="field.model"
                            :options="field.options"
                            :searchable="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :placeholder="`Select ${field.label}`"
                            label="name"
                            track-by="name"
                            class="w-full text-sm consistent-multiselect"
                            @input="onSelectChange(field)"
                          />

                          <input
                            v-else
                            :type="field.type"
                            v-model="field.model"
                            :id="field.name"
                            :disabled="field.disabled"
                            class="border rounded px-2 py-1 w-full"
                          />
                        </div>
                      </div>
                    </div>
                  </div>



                <!-- Save / Cancel Buttons -->
                <div class="flex justify-end gap-2 mt-4">
                  <button @click="$router.push('/pendingtickets')" class="px-4 py-2 btn-gradient-red rounded">
                    Cancel
                  </button>
                  <button @click="updateTicket" class="px-4 py-2 btn-gradient-green text-white rounded">
                   Update Ticket
                  </button>
                </div>

               <UpdateModal
                  :visible="showStatusModal"
                  title="Update Status"
                  @close="showStatusModal = false"
                  @save="handleStatusSave(newStatus)"
                >
                   <select
                      v-model="newStatus"
                      class="border rounded w-full px-2 py-1 mb-3"
                    >
                      <option disabled value="">Choose Status</option>
                      <option value="Waiting for defective unit from BP/EU">Waiting for defective unit from BP/EU</option>
                      <option value="Requested replacement to Vendor">Requested replacement to Vendor</option>
                      <option value="Unit for replacement approved">Unit for replacement approved</option>
                      <option value="Unit shipped to VST ECS Warehouse">Unit shipped to VST ECS Warehouse</option>
                      <option value="Replacement unit ready for pick up">Replacement unit ready for pick up</option>
                      <option value="Unit received by BP/EU">Unit received by BP/EU</option>
                      <option value="Custom">Custom Status</option>
                    </select>

                    <textarea
                      v-if="newStatus === 'Custom'"
                      v-model="customStatusText"
                      placeholder="Enter custom status..."
                      class="border rounded w-full mt-2 px-2 py-1"
                      rows="3"
                    ></textarea>

                  <template #footer>
                    <button class="btn-gradient-red px-4 py-2" @click="showStatusModal = false">Cancel</button>
                    <button class="btn-gradient-green px-4 py-2" @click="handleStatusSave(newStatus)">Save</button>
                  </template>
                </UpdateModal>


                <UpdateModal 
                :visible="showRemarksModal" 
                title="Add Remarks"
                @close="showRemarksModal = false" 
                @save="handleRemarksSave(newRemarks)">
                  <textarea v-model="newRemarks" placeholder="Enter remarks" class="border rounded w-full px-2 py-1" />
                  <template #footer>
                    <button class="btn-gradient-red px-4 py-2" @click="showRemarksModal = false">Cancel</button>
                    <button class="btn-gradient-green px-4 py-2" @click="handleRemarksSave(newRemarks)">Save</button>
                  </template>
                </UpdateModal>

                <UpdateModal 
                title="Edit Severity Level"
                :visible="showSeverityModal" 
                @close="showSeverityModal = false" 
                @save="handleSeveritySave(newSeverity)">
                  <select v-model="newSeverity" class="border rounded w-full px-2 py-1 mb-4">
                    <option disabled value="">Choose Severity Level</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                  </select>
                   <textarea v-model="newSeverityRemarks" placeholder="Enter reason" class="border rounded w-full px-2 py-1" />
                  <template #footer>
                    <button class="btn-gradient-red px-4 py-2" @click="showSeverityModal = false">Cancel</button>
                    <button class="btn-gradient-green px-4 py-2" @click="handleSeveritySave(newSeverity)">Save</button>
                  </template>
                </UpdateModal>

              </div>
            </div>
          </div>
        </div>
      </div>
  </main>
  </body>
</template>


<script src="../EditTicket.js"></script>
