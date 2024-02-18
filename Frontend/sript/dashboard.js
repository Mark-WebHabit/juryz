const authUser = document.querySelector(".auth-user");

const transaction = document.querySelector(".transactions");
const client = document.querySelector(".clients");
const driver = document.querySelector(".employees");
const vehicle = document.querySelector(".vehicles");

const windowScreen = window.innerWidth;
window.addEventListener("DOMContentLoaded", async () => {
  await getauthUSer();

  if (
    location.pathname ==
    "/Client/Frontend/views/authenticated_page/dashboard.html"
  ) {
    await getDatas();
  }
});

async function getauthUSer() {
  fetch("../../../Backend/controller/getauthuser.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        authUser.textContent = data.data.username;
      }
    })

    .catch((error) => console.error(error));
}

async function getDatas() {
  fetch("../../../Backend/controller/getallusers.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(client);
      client.textContent = `${data?.data.length || 0} Registered Clients`;
    })
    .catch((error) => console.error(error));

  fetch("../../../Backend/controller/gettransactions.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then(
      (data) =>
        (transaction.textContent = `${data?.data.length || 0} Transactions`)
    )
    .catch((error) => console.error(error));

  fetch("../../../Backend/controller/getdrivers.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then(
      (data) => (driver.textContent = `${data?.data.length || 0} Employees`)
    )
    .catch((error) => console.error(error));

  fetch("../../../Backend/controller/getvehiclecount.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then(
      (data) => (vehicle.textContent = `${data?.data.length || 0} Vehicles`)
    )
    .catch((error) => console.error(error));
}
