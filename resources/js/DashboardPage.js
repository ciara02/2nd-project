import { reactive, ref, onMounted, onUnmounted, nextTick  } from 'vue';
import Chart from 'chart.js/auto';
import Sidebar from './pages/Sidebar.vue';
import Navbar from './pages/Navbar.vue';
import Footer from './pages/Footer.vue';
import axios from 'axios';

export default {
  name: "DashboardPage",
  components: { Sidebar, Navbar, Footer },
  setup() {
    const stats = reactive({
      totalUsers: 0,
      pendingTickets: 0,
      resolvedTickets: 0,
      todayTickets: 0,
      requestTickets: 0
    });

     const ticketOverview = reactive({
      monthlyTickets: [],
      lastYearTickets: {},
      monthlyTicketsForChart: []
    });

   const percentChange = ref(0);
    const chartLine = ref(null);
    let chartInstance = null;
    const latestYear = ref(null);

    // ---------- Fetch Stats ----------
    const fetchStats = async () => {
      try {
        const res = await axios.get('/api/dashboard');
        Object.assign(stats, res.data);
      } catch (err) {
        console.error('Error fetching dashboard stats:', err);
      }
    };

 // ---------- Fetch Overview for Chart ----------
const fetchOverview = async () => {
  try {
    const res = await axios.get('/api/dashboard');
    const monthlyTickets = res.data.monthlyTickets || [];

    if (!monthlyTickets.length) {
      console.warn('No tickets available');
      ticketOverview.monthlyTicketsForChart = Array(12).fill(0);
      latestYear.value = null;
      percentChange.value = 0;
      createChart();
      return;
    }

    // Find the latest year with at least one ticket
    const years = monthlyTickets.map(t => parseInt(t.year));
    latestYear.value = Math.max(...years);

    // Prepare monthly data for the chart
    const monthlyData = Array(12).fill(0);
    monthlyTickets.forEach(item => {
      const { year, month, total } = item;
      if (parseInt(year) === latestYear.value) {
        const monthIndex = parseInt(month) - 1;
        monthlyData[monthIndex] = parseInt(total) || 0;
      }
    });

    ticketOverview.monthlyTicketsForChart = monthlyData;

    // Prepare previous year data for percent change
    const lastYearTickets = monthlyTickets.filter(t => parseInt(t.year) === latestYear.value - 1);
    const lastYearData = Array(12).fill(0);
    lastYearTickets.forEach(item => {
      const { month, total } = item;
      lastYearData[parseInt(month) - 1] = parseInt(total) || 0;
    });

    const thisYearTotal = monthlyData.reduce((a, b) => a + b, 0);
    const lastYearTotal = lastYearData.reduce((a, b) => a + b, 0);
    percentChange.value = lastYearTotal === 0 ? 0 : Math.round(((thisYearTotal - lastYearTotal) / lastYearTotal) * 100);

    createChart();
  } catch (err) {
    console.error('Error fetching overview:', err);
  }
};

    // ---------- Create Chart ----------

    const createChart = async () => {
      await nextTick(); // wait until canvas is rendered

      if (!chartLine.value) return;

      // destroy previous chart if exists
      if (chartInstance) chartInstance.destroy();

      const ctx = chartLine.value.getContext('2d');
      ctx.canvas.height = 350;

      chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
          datasets: [{
            label: 'Tickets',
            data: ticketOverview.monthlyTicketsForChart,
            borderColor: '#4ade80',
            backgroundColor: ctx => {
              const chartArea = ctx.chart.chartArea;
              if (!chartArea) return null;
              const gradient = ctx.chart.ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
              gradient.addColorStop(0, 'rgba(74, 222, 128, 0.4)');
              gradient.addColorStop(1, 'rgba(74, 222, 128, 0)');
              return gradient;
            },
            fill: true,
            tension: 0.4
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });
    };
    const currentYear = new Date().getFullYear();


    // ---------- Slides ----------
    const slides = ref([
      { img: "/img/Whisk_c048af3681.jpg", icon: "ni ni-folder-17", title: "View Pending Tickets", text: "Easily track all your pending tickets." },
      { img: "/img/Whisk_41465ec491.jpg", icon: "ni ni-check-bold", title: "Resolved Tickets Overview", text: "Monitor completed tickets." },
      { img: "/img/Whisk_a57bb7857c.jpg", icon: "ni ni-paper-diploma", title: "Create New Requests", text: "Submit new tickets efficiently." }
    ]);

    const currentSlide = ref(0);
    let interval = null;

    const startSlideShow = () => {
      interval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.value.length;
      }, 5000);
    };
    const stopSlideShow = () => clearInterval(interval);

    onMounted(() => {
      fetchStats();
      fetchOverview();
      startSlideShow();
    });

    onUnmounted(() => {
      stopSlideShow();
    });

    return {
      stats,
      ticketOverview,
      currentYear,
      percentChange,
      chartLine,
      slides,
      currentSlide,
      latestYear
    };
  }
};
