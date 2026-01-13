// ------------------------------
// Funciones generales para TODAS las páginas
// ------------------------------

// Botón volver (si existe en la página)
document.addEventListener("DOMContentLoaded", () => {
  const btnVolver = document.getElementById("btnVolver");
  if (btnVolver) {
    btnVolver.addEventListener("click", () => {
      window.location.href = "index.html";
    });
  }
});

// Animación de scroll suave para enlaces internos
document.querySelectorAll('a[href^="#"]').forEach(enlace => {
  enlace.addEventListener("click", function(e) {
    e.preventDefault();
    const destino = document.querySelector(this.getAttribute("href"));
    if (destino) {
      destino.scrollIntoView({ behavior: "smooth" });
    }
  });
});

// Mensaje de consola para verificar carga
console.log("✅ main.js cargado correctamente");
