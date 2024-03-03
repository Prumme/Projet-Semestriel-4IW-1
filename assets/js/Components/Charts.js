import { Component } from "./Component";
import { DateTime } from "luxon";

export class LineChart extends Component {
  constructor(...args) {
    super(...args);
  }

  onMount() {
    const ctx = this.element;
    const datasets1 = JSON.parse(this.element.dataset.chartDataset);

    let primaryColor = getComputedStyle(
      document.documentElement
    ).getPropertyValue("--primary-300");

    let labels = [];
    let datasets = [];

    for (let i = 0; i < datasets1.length; i++) {
      labels.push(datasets1[i].day);
      datasets.push(datasets1[i].total_amount);
    }

    new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "All Quotes",
            data: datasets,
            borderColor: primaryColor,
            backgroundColor: "transparent",
            pointBorderWidth: 2,
            tension: 0.3,
          },
        ],
      },
      options: {
        scales: {
          x: {
            type: "category",
            labels: labels,
            title: {
              display: false,
            },
            grid: {
              display: false,
            },
          },
          y: {
            title: {
              display: false,
            },
            ticks: {
              callback: (value) => "€ " + value,
            },
          },
        },
      },
    });
  }
}

export class BarChart extends Component {
  constructor(...args) {
    super(...args);
  }

  onMount() {
    const ctx = this.element;
    const data = JSON.parse(this.element.dataset.chartDataset);

    let primaryColor = getComputedStyle(
      document.documentElement
    ).getPropertyValue("--primary-300");

    let secondaryColor = getComputedStyle(
      document.documentElement
    ).getPropertyValue("--primary-600");

    let labels = [];
    let datasets = [];

    for (let i = 0; i < data.length; i++) {
      labels.push(data[i].product_name);
      datasets.push(data[i].total_quantity);
    }

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Sales",
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
