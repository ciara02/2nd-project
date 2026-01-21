<template>
 <body class="m-0 font-sans antialiased font-normal bg-white text-start text-base leading-default text-slate-500">
      <div class="container sticky top-0 z-sticky">
      <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 flex-0">
          <!-- Navbar -->
          <nav class="absolute top-0 left-0 right-0 z-30 flex flex-wrap items-center px-4 py-2 m-6 mb-0 shadow-sm rounded-xl bg-white/80 backdrop-blur-2xl backdrop-saturate-200 lg:flex-nowrap lg:justify-start">
            <div class="flex items-center justify-between w-full p-0 px-6 mx-auto flex-wrap-inherit">
               <img src="/img/official-logo-cropped.png" class="inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-8" alt="main_logo" />
              <a class="py-1.75 text-sm mr-4 ml-4 whitespace-nowrap font-bold text-slate-700 lg:ml-2" href="https://demos.creative-tim.com/argon-dashboard-tailwind/pages/dashboard.html" target="_blank"> iSupport Ticketing System </a>
              <button navbar-trigger class="px-3 py-1 ml-2 leading-none transition-all ease-in-out bg-transparent border border-transparent border-solid rounded-lg shadow-none cursor-pointer text-lg lg:hidden" type="button" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="inline-block mt-2 align-middle bg-center bg-no-repeat bg-cover w-6 h-6 bg-none">
                  <span bar1 class="w-5.5 rounded-xs relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"></span>
                  <span bar2 class="w-5.5 rounded-xs mt-1.75 relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"></span>
                  <span bar3 class="w-5.5 rounded-xs mt-1.75 relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"></span>
                </span>
              </button>
              <div navbar-menu class="items-center flex-grow transition-all duration-500 lg-max:overflow-hidden ease lg-max:max-h-0 basis-full lg:flex lg:basis-auto">
                <ul class="flex flex-col pl-0 mx-auto mb-0 list-none lg:flex-row xl:ml-auto">
                </ul>
                <ul class="hidden pl-0 mb-0 list-none lg:block lg:flex-row">
                  <li>
                    <router-link
                      to="/login"
                      class="block px-4 py-2 mr-2 font-normal transition-all ease-in-out duration-250 text-sm text-slate-700 lg:px-2"
                    >
                      <i class="mr-1 fas fa-key opacity-60"></i>
                      Sign In
                    </router-link>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>

    <main class="flex items-center justify-center min-h-screen"> 
        <div class="w-full md:w-8/12 px-6 py-6 mx-auto "> 
          <div class="flex flex-wrap -mx-3" :style="{ marginTop: '70px' }">
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
                                    <input
                                      type="file"
                                      :name="field.name"
                                      :id="`fileInput-${field.name}`"
                                      @change="handleFileUpload($event, field)"
                                      style="display: none;"
                                    />

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
                              <button
                                @click="addTicket"
                                :class="[
                                  'px-4 py-2 rounded text-white font-medium transition',
                                  'btn-gradient-green',
                                ]"
                              >
                                Request Ticket
                              </button>
                            </div>

                        </div>
                      </div>
                    </div>
                 </div>
              <footer class="pt-4">
                <div class="w-full px-6 mx-auto">
                  <div class="flex flex-wrap items-center -mx-3 justify-center">
                    <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
                      <div class="text-sm leading-normal text-center text-slate-500">
                        © {{ currentYear }} VST ECS Phils., Inc. - All Rights Reserved
                      </div>
                    </div>
                  </div>
                </div>
              </footer>

      </div>
    </main>
  </body>
</template>

<script src="../ClientAddNewTicket.js">

</script>
