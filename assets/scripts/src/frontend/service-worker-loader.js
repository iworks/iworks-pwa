if ("serviceWorker" in navigator) {
  navigator.serviceWorker
    .register(window.iworks_pwa.serviceWorkerUri)
    .then(function (reg) {
      // "New version available" toast (feature: SKIP_WAITING prompt).
      function showUpdateToast(worker) {
        if (!worker || document.getElementById("iworks-pwa-update-toast")) {
          return;
        }
        var container = document.createElement("div");
        container.id = "iworks-pwa-update-toast";
        container.setAttribute("role", "alert");
        var message = document.createElement("span");
        message.className = "iworks-pwa-update-toast__message";
        message.textContent =
          window.iworks_pwa.updateMessage || "New version available.";
        var button = document.createElement("button");
        button.type = "button";
        button.className = "iworks-pwa-update-toast__button";
        button.textContent = window.iworks_pwa.updateButtonLabel || "Refresh";
        button.addEventListener("click", function () {
          if (!worker) {
            return;
          }
          worker.addEventListener("statechange", function () {
            if ("activated" === worker.state) {
              window.location.reload();
            }
          });
          worker.postMessage({ type: "SKIP_WAITING" });
          container.remove();
        });
        container.appendChild(message);
        container.appendChild(button);
        document.body.appendChild(container);
      }
      if (reg.waiting && navigator.serviceWorker.controller) {
        showUpdateToast(reg.waiting);
      }
      reg.addEventListener("updatefound", function () {
        var installing = reg.installing;
        if (!installing) {
          return;
        }
        installing.addEventListener("statechange", function () {
          if (
            "installed" === installing.state &&
            navigator.serviceWorker.controller
          ) {
            showUpdateToast(installing);
          }
        });
      });
    })
    .catch(function (err) {});
}
