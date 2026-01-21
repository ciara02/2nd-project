<template>
  <body class="m-0 font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    <!-- sidenav  -->
  <div class="flex">
    <Sidebar />
    <main class="flex-1 ml-64">
    </main>
  </div>
    <main class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl">
       <Navbar />
      <!-- cards -->
      <div class="w-full px-6 py-6 mx-auto">
        <!-- row 1 -->
        <div class="flex flex-wrap -mx-3">
          <!-- card1 -->
          <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
              <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                  <div class="flex-none w-2/3 max-w-full px-3">
                    <div>
                      <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">PENDING TICKETS</p>
                      <h5 class="mb-2 font-bold dark:text-white">{{ stats.pendingTickets  }}</h5>
                    </div>
                  </div>
                  <div class="px-3 text-right basis-1/3">
                    <div class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-blue-500 to-violet-500">
                      <i class="ni leading-none ni-time-alarm text-lg relative top-3.5 text-white"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- card2 -->
          <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
              <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                  <div class="flex-none w-2/3 max-w-full px-3">
                    <div>
                      <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">RESOLVED TICKETS</p>
                      <h5 class="mb-2 font-bold dark:text-white"> {{ stats.resolvedTickets }}</h5>
                    </div>
                  </div>
                  <div class="px-3 text-right basis-1/3">
                    <div class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-red-600 to-orange-600">
                      <i class="ni leading-none ni-check-bold text-lg relative top-3.5 text-white"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- card3 -->
          <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
              <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                  <div class="flex-none w-2/3 max-w-full px-3">
                    <div>
                      <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">TODAY'S TICKET/S</p>
                      <h5 class="mb-2 font-bold dark:text-white">{{ stats.todayTickets }}</h5>
                    </div>
                  </div>
                  <div class="px-3 text-right basis-1/3">
                    <div class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-emerald-500 to-teal-400">
                      <i class="ni leading-none ni-calendar-grid-58 text-lg relative top-3.5 text-white"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- card4 -->
          <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
              <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                  <div class="flex-none w-2/3 max-w-full px-3">
                    <div>
                      <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">REQUEST TICKETS</p>
                      <h5 class="mb-2 font-bold dark:text-white">{{ stats.requestTickets }}</h5>
                    </div>
                  </div>
                  <div class="px-3 text-right basis-1/3">
                    <div class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-orange-500 to-yellow-500">
                      <i class="ni leading-none ni-send text-lg relative top-3.5 text-white"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- cards row 2 -->
        <div class="flex flex-wrap mt-6 -mx-3">
          <div class="w-full max-w-full px-3 mt-0 lg:w-7/12 lg:flex-none">
            <div class="border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
              <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-0">
                <h6 class="capitalize dark:text-white">Tickets overview </h6>
                <p class="mb-0 text-sm leading-normal dark:text-white dark:opacity-60">
                  <i :class="percentChange >= 0 ? 'fa fa-arrow-up text-emerald-500' : 'fa fa-arrow-down text-red-500'"></i>
                  <span class="font-semibold">
                    {{ percentChange >= 0 ? '+' : '' }}{{ percentChange }}% <span v-if="latestYear">- {{ latestYear }}</span>
                  </span>
                </p>
              </div>
              <div class="flex-auto p-4">
                 <div class="h-30">
                    <canvas ref="chartLine"></canvas>
                  </div>
              </div>
            </div>
          </div>

           <div class="w-full max-w-full px-3 lg:w-5/12 lg:flex-none">
            <div class="relative w-full h-full overflow-hidden rounded-2xl">
              <div
                v-for="(slide, index) in slides"
                :key="index"
                class="absolute w-full h-full transition-all duration-500"
                :class="{ 'opacity-100 z-10': currentSlide === index, 'opacity-0 z-0': currentSlide !== index }"
              >
                <img class="object-cover h-full w-full" :src="slide.img" alt="carousel image" />
                
                <div class="absolute left-0 bottom-0 right-[15%] ml-12 pt-5 pb-5">
                  <div class="bg-black bg-opacity-60 p-4 rounded-lg text-white" style="background-color: rgba(0, 0, 0, 0.2);">
                    <div class="inline-block w-8 h-8 mb-4 text-center text-black bg-white rounded-lg">
                      <i class="top-0.75 text-xxs relative text-slate-700" :class="slide.icon"></i>
                    </div>
                    <h5 class="mb-1 text-white">{{ slide.title }}</h5>
                    <p class="text-sm">{{ slide.text }}</p>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>

        <Footer />
       
      </div>
      <!-- end cards -->
    </main>
  </body>
</template>

<script src="../DashboardPage.js"></script>








