<template>
  <body class="m-0 font-sans antialiased font-normal bg-white text-start text-base leading-default text-slate-500">

    <main class="mt-0 transition-all duration-200 ease-in-out">
      <section>
        <div class="relative flex items-center min-h-screen p-0 overflow-hidden bg-center bg-cover">
          <div class="container z-1">
            <div class="flex flex-wrap -mx-3">
              <div class="flex flex-col w-full max-w-full px-3 mx-auto lg:mx-0 shrink-0 md:flex-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
                <div class="relative flex flex-col min-w-0 break-words bg-transparent border-0 shadow-none lg:py-4 dark:bg-gray-950 rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0">
                    <h4 class="font-bold">Sign In</h4>
                    <p class="mb-0">Enter your email and password to sign in</p>
                    <div class="flex-auto p-6">
                   <form @submit.prevent="submitLogin" role="form">
                      <div class="mb-4">
                        <input
                          v-model="email"
                          type="email"
                          placeholder="Email"
                          class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                          required
                        />
                      </div>

                      <div class="mb-4">
                        <input
                          v-model="password"
                          type="password"
                          placeholder="Password"
                          class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                          required
                        />
                      </div>

                      <button
                        type="submit"
                        class="inline-block w-full px-16 py-3.5 mt-6 mb-0 font-bold leading-normal text-center text-white align-middle transition-all bg-blue-500 border-0 rounded-lg cursor-pointer hover:-translate-y-px active:opacity-85 hover:shadow-xs text-sm ease-in tracking-tight-rem shadow-md bg-150 bg-x-25"
                      >
                        Sign in
                      </button>
                    </form>
                      <div class="text-center">
                        <a :href="azureLoginUrl" class="azure-btn">
                          <!-- Azure icon -->
                          <svg xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 24 24"
                              fill="currentColor"
                              class="azure-icon">
                            <path d="M12 0L1.605 6v12L12 24l10.395-6V6zM12 3.6l7.79 4.5v7.8l-7.79 4.5-7.79-4.5V8.1z"/>
                          </svg>

                          <span>Sign in with Azure AD</span>
                        </a>
                        </div>
                  </div>
                    </div>
                </div>
              </div>
              <div class="absolute top-0 right-0 flex-col justify-center hidden w-6/12 h-full max-w-full px-3 pr-0 my-auto text-center flex-0 lg:flex">
                <div class="relative flex flex-col justify-center h-full px-24 m-4 overflow-hidden rounded-xl login-bg">
                  <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-blue-500 to-violet-500 opacity-60"></span>
                  <h4 class="z-20 mt-12 font-bold text-white">"Your streamlined solution for iSupport ticket creation"</h4>
                  <p class="z-20 text-white ">Create, track, and manage your support tickets efficiently with iSupport Ticketing.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer class="py-12">
      <div class="container">
        <div class="flex flex-wrap -mx-3">
          <div class="flex-shrink-0 w-full max-w-full mx-auto mb-6 text-center lg:flex-0 lg:w-8/12">
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> Company </a>
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> About Us </a>
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> Team </a>
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> Products </a>
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> Blog </a>
            <a href="javascript:;" target="_blank" class="mb-2 mr-4 text-slate-400 sm:mb-0 xl:mr-12"> Pricing </a>
          </div>
          <div class="flex-shrink-0 w-full max-w-full mx-auto mt-2 mb-6 text-center lg:flex-0 lg:w-8/12">
            <a href="javascript:;" target="_blank" class="mr-6 text-slate-400">
              <span class="text-lg fab fa-dribbble"></span>
            </a>
            <a href="javascript:;" target="_blank" class="mr-6 text-slate-400">
              <span class="text-lg fab fa-twitter"></span>
            </a>
            <a href="javascript:;" target="_blank" class="mr-6 text-slate-400">
              <span class="text-lg fab fa-instagram"></span>
            </a>
            <a href="javascript:;" target="_blank" class="mr-6 text-slate-400">
              <span class="text-lg fab fa-pinterest"></span>
            </a>
            <a href="javascript:;" target="_blank" class="mr-6 text-slate-400">
              <span class="text-lg fab fa-github"></span>
            </a>
          </div>
        </div>
        <div class="flex flex-wrap -mx-3">
          <div class="w-8/12 max-w-full px-3 mx-auto mt-1 text-center flex-0">
            <p class="mb-0 text-slate-400">
              Copyright © {{ currentYear }}
              VST ECS Phils., Inc. - All Rights Reserved
            </p>
          </div>
        </div>
      </div>
    </footer>
  </body>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'App',
  data() {
    return {
      currentYear: new Date().getFullYear(),
      azureLoginUrl: window.location.origin + '/login/redirect/azure',
      email: '',
      password: '',
      remember: false,
    };
  },
  methods: {
   async submitLogin() {
    try {
      const res = await axios.post('/api/login', {
        email: this.email,
        password: this.password,
        remember: this.remember,
      });

      if (res.data.success) {
        // Redirect based on role
        if (res.data.role == '1') {
          window.location.href = '/dashboard';
        } else {
          window.location.href = '/'; // normal user landing page
        }
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: err.response?.data?.message || 'Something went wrong!',
        timer: 1500,
        showConfirmButton: false,
        showCloseButton: true, // allow manual close
      });
    }
  },
  },
};
</script>
