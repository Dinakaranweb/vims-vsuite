"use strict";

// This file is only needed on the admin ticket/department dashboard.
// Guard against running on other pages where these variables don't exist.
if (typeof departmentData === 'undefined' || !document.getElementById('myChart4')) {
  // not on the admin dashboard — do nothing
} else {

const departments = departmentData.map(item => item.name);
const ticketCounts = departmentData.map(item => item.count);
const postCount = departmentData.map(item => item.count);

let raisedTo, raisedCounts, ticketColors;
if (typeof ticketData !== 'undefined') {
  raisedTo = ticketData.map(item => item.name);
  raisedCounts = ticketData.map(item => item.count);

  ticketColors = departments.map(() => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgb(${r}, ${g}, ${b})`;
  });
}

const backgroundColors = departments.map(() => {
  const r = Math.floor(Math.random() * 256);
  const g = Math.floor(Math.random() * 256);
  const b = Math.floor(Math.random() * 256);
  return `rgb(${r}, ${g}, ${b})`;
});

var ctx = document.getElementById("myChart4").getContext('2d');
var myChart2 = new Chart(ctx, {
  type: 'pie',
  data: {
    datasets: [{
      data: [
        open_tickets,
        closed_tickets,
        tickets_on_hold,
        tickets_in_progress,
        completed_tickets,
      ],
      backgroundColor: [
        '#2a6d98',
        '#63ed7a',
        '#fc544b',
        '#ffa426',
        '#000',
      ],
      label: 'Tickets by Status'
    }],
    labels: [
      'Open',
      'Closed',
      'On Hold',
      'In Progress',
      'Completed'
    ],
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom' } },
  }
});

var ctx2 = document.getElementById("myChart3") ? document.getElementById("myChart3").getContext('2d') : null;
if (ctx2) {
  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: ticketCounts,
        backgroundColor: backgroundColors,
        label: 'Tickets by Department'
      }],
      labels: departments,
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
    }
  });
}

var ctx3 = document.getElementById("myChart9") ? document.getElementById("myChart9").getContext('2d') : null;
if (ctx3) {
  new Chart(ctx3, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: raisedCounts,
        backgroundColor: ticketColors,
        label: 'Tickets Raised'
      }],
      labels: raisedTo,
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
    }
  });
}

var ctx4 = document.getElementById("myChart10") ? document.getElementById("myChart10").getContext('2d') : null;
if (ctx4) {
  new Chart(ctx4, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: typeof departmentCounts !== 'undefined' ? departmentCounts : [],
        backgroundColor: backgroundColors,
        label: 'Post by Department'
      }],
      labels: typeof departmentNames !== 'undefined' ? departmentNames : [],
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
    }
  });
}

} // end guard
