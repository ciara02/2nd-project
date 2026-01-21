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
                  <p class="uppercase text-sm mb-4">Ticket Details</p>
                  <div class="flex flex-wrap -mx-3">
                    <!-- Left Card -->
                    <div class="w-full md:w-6/12 px-3 mb-6">
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
                    <!-- Right Card -->
                    <div class="w-full md:w-6/12 px-3 mb-6">
                      
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
                 <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                     <div class="w-full ">
                      
                        <div v-for="field in concernField" :key="field.name" class="mb-4">
                        <label 
                            :for="field.name" 
                            class="text-right font-bold text-xs text-slate-700 dark:text-white/80"
                        >
                            {{ field.label }}
                        </label>

                        <!-- Textarea field -->
                        <textarea
                            v-if="field.type === 'textarea'"
                            v-model="field.model"
                            :id="field.name"
                            class="border rounded px-2 py-1 w-full"
                        ></textarea>

                       <div class="relative w-full" v-if="field.type === 'file'">
                          <!-- Hidden native input -->
                          <input
                            type="file"
                            :name="field.name"
                            :id="`fileInput-${field.name}`"
                            @change="handleFileUpload($event, field)"
                            style="display: none;"
                          />

                          <!-- Styled label with inline styles -->
                          <label
                            :for="`fileInput-${field.name}`"
                            style="
                              display: flex;
                              align-items: center;
                              border: 1px solid #ced4da;
                              border-radius: 0.25rem;
                              padding: 0.375rem 0.75rem;
                              background-color: #fff;
                              cursor: pointer;
                              font-size: 0.875rem;
                              color: #495057;
                            "
                            @mouseover="hover = true"
                            @mouseleave="hover = false"
                          >
                            <span style="margin-right: 0.5rem;">Choose file</span>
                            <span
                              style="
                                padding-left: 0.5rem;
                                border-left: 1px solid #ced4da;
                                color: #6c757d;
                              "
                            >
                              {{ field.selectedFileName || 'No file chosen' }}
                            </span>
                          </label>
                        </div>
                        </div>

                      
                    </div>

                <div class="flex justify-end gap-2 mt-4">
                  <button @click="$router.push('/pendingtickets')" class="px-4 py-2 btn-gradient-red rounded">
                    Cancel
                  </button>
                  <button @click="addTicket" class="px-4 py-2 btn-gradient-green text-white rounded">
                   Add Ticket
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

<script src="../AddNewTicket.js">

</script>
