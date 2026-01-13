// ------------------------------
// Variables globales
// ------------------------------
const carrito = [];
let productoSeleccionado = "";
let precioSeleccionado = 0;
let categoriaSeleccionada = "";
let cantidadSeleccionada = 1;

// ------------------------------
// Datos de productos
// ------------------------------
const productosMasculinos = [
  { nombre: "Fresh Rose", precio: 15000, img: "Imagenes/Fresh_Rose.jpg" },
  { nombre: "Pude Ride", precio: 15000, img: "Imagenes/Pude Ride.jpg" },
  { nombre: "Fresandia", precio: 17000, img: "Imagenes/Fresandia.jpg" },
  { nombre: "Aurum Boreal", precio: 20000, img: "Imagenes/Aurumboreal.jpg" },
  { nombre: "IV Glace", precio: 15000, img: "Imagenes/IV_Glace.jpg" },
];

// ------------------------------
// Abrir modal de productos
// ------------------------------
function abrirModal(tipo) {
  const modal = document.getElementById("modal-productos");
  const contenedor = document.getElementById("contenedor-productos-modal");
  contenedor.innerHTML = "";

  const productos = tipo === "masculino" ? productosMasculinos : productosFemeninos;

  productos.forEach(prod => {
    const div = document.createElement("div");
    div.classList.add("producto");
    div.innerHTML = `
      <img src="${prod.img}" alt="${prod.nombre}">
      <h3>${prod.nombre}</h3>
      <p>$${prod.precio.toLocaleString()}</p>
      <button onclick="mostrarModalUnidades('${prod.nombre}', ${prod.precio}, '${tipo}')">
        Agregar al carrito 🛒
      </button>
    `;
    contenedor.appendChild(div);
  });

  modal.style.display = "flex";
}

function cerrarModal() {
  document.getElementById("modal-productos").style.display = "none";
}

// ------------------------------
// Modal unidades
// ------------------------------
function mostrarModalUnidades(nombre, precio, categoria) {
  productoSeleccionado = nombre;
  precioSeleccionado = precio;
  categoriaSeleccionada = categoria;
  cantidadSeleccionada = 1;

  document.getElementById("unidades-mostradas").textContent = cantidadSeleccionada;
  document.getElementById("error-unidades").style.display = "none";
  document.getElementById("modal-unidades").style.display = "flex";
}

function cambiarUnidades(valor) {
  cantidadSeleccionada += valor;
  if (cantidadSeleccionada < 1) cantidadSeleccionada = 1;
  document.getElementById("unidades-mostradas").textContent = cantidadSeleccionada;
}

function cerrarModalUnidades() {
  document.getElementById("modal-unidades").style.display = "none";
}

// ------------------------------
// Confirmar unidades y agregar al carrito (sumando si ya existe)
// ------------------------------
function confirmarUnidades() {
  const unidades = cantidadSeleccionada;
  const error = document.getElementById("error-unidades");

  if (isNaN(unidades) || unidades < 1) {
    error.style.display = "block";
    return;
  }

  const indexExistente = carrito.findIndex(item => item.nombre === productoSeleccionado);

  if (indexExistente !== -1) {
    carrito[indexExistente].cantidad += unidades;
    mostrarAnimacion(`Ahora tienes ${carrito[indexExistente].cantidad} unidades de ${productoSeleccionado} en el carrito`);
  } else {
    carrito.push({
      nombre: productoSeleccionado,
      precio: precioSeleccionado,
      cantidad: unidades,
      categoria: categoriaSeleccionada
    });
    mostrarAnimacion(`${productoSeleccionado} x${unidades} agregado al carrito`);
  }

  mostrarResumen();
  cerrarModalUnidades();
}

// ------------------------------
// Animación de mensaje flotante
// ------------------------------
function mostrarAnimacion(mensaje) {
  const mensajeDiv = document.getElementById("mensaje-carrito");
  mensajeDiv.textContent = mensaje;
  mensajeDiv.classList.add("visible");

  setTimeout(() => {
    mensajeDiv.classList.remove("visible");
  }, 2000);
}

// ------------------------------
// Modal resumen del carrito
// ------------------------------
function mostrarResumen() {
  const resumenDiv = document.getElementById("contenido-resumen");
  const totalDiv = document.getElementById("total-resumen");

  resumenDiv.innerHTML = "";
  let total = 0;

  carrito.forEach((item, index) => {
    const linea = document.createElement("div");
    linea.classList.add("linea-carrito");
    linea.innerHTML = `
      <span>${item.nombre} (${item.categoria}) x${item.cantidad} = $${(item.precio * item.cantidad).toLocaleString()}</span>
      <button onclick="eliminarProducto(${index})">Eliminar ❌</button>
    `;
    resumenDiv.appendChild(linea);
    total += item.precio * item.cantidad;
  });

  totalDiv.textContent = `Total: $${total.toLocaleString()}`;
  document.getElementById("modal-resumen").style.display = "flex";
}

function cerrarResumen() {
  document.getElementById("modal-resumen").style.display = "none";
}

// ------------------------------
// Eliminar producto del carrito
// ------------------------------
function eliminarProducto(index) {
  carrito.splice(index, 1);
  mostrarAnimacion("Producto eliminado del carrito");
  mostrarResumen();
}

// ------------------------------
// Finalizar compra
// ------------------------------
function finalizarCompra() {
  localStorage.setItem("carrito", JSON.stringify(carrito));
  window.open("factura.html");
}

console.log("✅ IV.js cargado correctamente");
