import { Component } from "./Component";

export class BarChart extends Component {
  constructor(...args) {
    super(...args);
  }

  onMount() {
    const ctx = this.element;

    const data = JSON.parse(this.element.dataset.chartDataset);
    const legend = this.element.dataset.chartLegend;

    let primaryColor = getComputedStyle(
      document.documentElement
    ).getPropertyValue("--primary-300");

    let secondaryColor = getComputedStyle(
      document.documentElement
    ).getPropertyValue("--primary-600");

    let labels = [];
    let datasets = [];

    for (let i = 0; i < data.length; i++) {
      labels.push(data[i].label);
      datasets.push(data[i].data);
    }

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: legend,
            data: datasets,
            backgroundColor: primaryColor,
            borderColor: secondaryColor,
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }
}

export class DoughnutChart extends Component {
  constructor(...args) {
    super(...args);
  }

  onMount() {
    const ctx = this.element;

    const data = JSON.parse(this.element.dataset.chartDataset);

    let labels = [];
    let datasets = [];

    for (let i = 0; i < data.length; i++) {
      labels.push(data[i].label);
      datasets.push(data[i].data);
    }

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [
          {
            data: datasets,
            backgroundColor: [
              "rgb(255, 99, 132)",
              "rgb(54, 162, 235)",
              "rgb(255, 205, 86)",
              "rgb(75, 192, 192)",
              "rgb(153, 102, 255)",
              "rgb(255, 159, 64)",
            ],
          },
        ],
      },
      options: {
        plugins: {
          legend: {
            display: false,
            // position: "right",
          },
        },
      },
    });
  }
}
