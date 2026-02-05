document.addEventListener("DOMContentLoaded", () => {

    const contBotones = document.getElementById("gala-botones");
    const contContenido = document.getElementById("gala-contenido");

    let galaActual = null;

    // ============================
    // CARGAR GALA
    // ============================
    async function cargarGala() {
        contBotones.innerHTML = "";
        contContenido.innerHTML = "<p>Cargando gala...</p>";

        try {
            const res = await fetch("../php/gala-obtener.php");
            const data = await res.json();

            if (!data.ok) {
                contContenido.innerHTML = `
                    <p>No existe ninguna gala creada.</p>
                    <div class="gala-centrado">
                        <button class="btn login-btn" onclick="location.href='gala-nueva.html'">
                            Crear gala
                        </button>
                    </div>
                `;
                return;
            }

            galaActual = data.data;

            contBotones.innerHTML = `
                <button class="btn login-btn" id="btn-pre">Pre‑evento</button>
                <button class="btn login-btn" id="btn-post">Post‑evento</button>
            `;

            document.getElementById("btn-pre").addEventListener("click", mostrarPreEvento);
            document.getElementById("btn-post").addEventListener("click", mostrarPostEvento);

            mostrarPreEvento();

        } catch (err) {
            console.error(err);
            contContenido.innerHTML = "<p>Error al cargar la gala.</p>";
        }
    }

    // ============================
    // MODO PRE‑EVENTO
    // ============================
    async function mostrarPreEvento() {
        if (!galaActual) return;

        contContenido.innerHTML = `
            <div class="panel-card">
                <h3>${galaActual.titulo}</h3>
                <p><strong>Fecha:</strong> ${galaActual.fecha}</p>
                <p><strong>Hora:</strong> ${galaActual.hora}</p>
                <p><strong>Lugar:</strong> ${galaActual.lugar}</p>
                <p><strong>Descripción:</strong> ${galaActual.descripcion ?? ""}</p>
                ${galaActual.imagen ? `<div class="gala-imagen-container">
                    <img src="../uploads/${galaActual.imagen}" alt="Imagen gala" class="gala-imagen">
                </div>` : ""}
                <div class="gala-acciones">
                    <button class="btn login-btn" onclick="location.href='gala-editar.html'">Editar gala</button>
                    <button class="btn login-btn" onclick="location.href='gala-seccion-nueva.html?id_gala=${galaActual.id}'">Añadir sección</button>
                </div>
            </div>

            <div class="panel-card panel-card-separado">
                <h3>Secciones de la gala</h3>
                <div id="lista-secciones">
                    <p>Cargando secciones...</p>
                </div>
            </div>
        `;

        cargarSecciones();
    }

    async function cargarSecciones() {
        const cont = document.getElementById("lista-secciones");
        if (!galaActual) return;

        try {
            const res = await fetch(`../php/gala-secciones-listar.php?id_gala=${galaActual.id}`);
            const data = await res.json();

            if (!data.ok) {
                cont.innerHTML = `<p>No se han podido cargar las secciones.</p>`;
                return;
            }

            if (!data.data.length) {
                cont.innerHTML = `<p>No hay secciones creadas todavía.</p>`;
                return;
            }

            cont.innerHTML = data.data.map(s => `
                <div class="panel-item">
                    <h4>${s.titulo}</h4>
                    <p><strong>Hora:</strong> ${s.hora}</p>
                    <p><strong>Sala:</strong> ${s.sala}</p>
                    <p>${s.descripcion ?? ""}</p>
                    <button class="btn login-btn" onclick="location.href='gala-seccion-editar.html?id=${s.id}&id_gala=${galaActual.id}'">
                        Editar sección
                    </button>
                </div>
            `).join("");
        } catch (err) {
            console.error(err);
            cont.innerHTML = `<p>Error al cargar las secciones.</p>`;
        }
    }

    // ============================
    // MODO POST‑EVENTO
    // ============================
    async function mostrarPostEvento() {
        if (!galaActual) return;

        contContenido.innerHTML = `
            <div class="panel-card">
                <h3>Post‑evento</h3>
                <p>Escribe aquí un pequeño resumen de cómo ha sido la gala.</p>
                <textarea id="post-texto" rows="5" class="gala-textarea">${galaActual.post_evento_texto ?? ""}</textarea>
                <div class="gala-acciones">
                    <button class="btn login-btn" id="btn-guardar-post">Guardar texto</button>
                </div>
                <div id="post-texto-error" class="error-campo"></div>
            </div>

            <div class="panel-card panel-card-separado">
                <h3>Ganadores</h3>
                <div id="lista-ganadores">
                    <p>Cargando ganadores...</p>
                </div>
            </div>

            <div class="panel-card panel-card-separado">
                <h3>Galería de imágenes</h3>
                <form id="form-galeria" enctype="multipart/form-data" class="gala-form-galeria">
                    <input type="file" id="imagen-galeria" name="imagen" accept="image/*">
                    <button type="submit" class="btn login-btn">Subir imagen</button>
                    <div id="galeria-error" class="error-campo"></div>
                </form>
                <div id="galeria-imagenes" class="gala-galeria">
                    <p>Cargando galería...</p>
                </div>
            </div>
        `;

        document.getElementById("btn-guardar-post").addEventListener("click", guardarTextoPostEvento);
        document.getElementById("form-galeria").addEventListener("submit", subirImagenGaleria);

        cargarGanadores();
        cargarGaleria();
    }

    async function guardarTextoPostEvento() {
        const textarea = document.getElementById("post-texto");
        const error = document.getElementById("post-texto-error");
        error.textContent = "";

        const texto = textarea.value.trim();

        try {
            const res = await fetch("../php/gala-post-guardar-texto.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ texto })
            });

            const r = await res.json();

            if (r.ok) {
                galaActual.post_evento_texto = texto;
                await Swal.fire({
                    icon: "success",
                    title: "Texto guardado",
                    text: "El resumen del post‑evento se ha guardado correctamente."
                });
            } else {
                error.textContent = r.msg || "No se ha podido guardar el texto.";
            }
        } catch (err) {
            console.error(err);
            error.textContent = "Error al guardar el texto.";
        }
    }

    async function cargarGanadores() {
        const cont = document.getElementById("lista-ganadores");

        try {
            const res = await fetch("../php/ganadores-listar.php");
            const data = await res.json();

            if (!data.ok || !data.data.length) {
                cont.innerHTML = "<p>No hay ganadores registrados todavía.</p>";
                return;
            }

            cont.innerHTML = data.data.map(g => `
                <div class="panel-item">
                    <p><strong>Categoría:</strong> ${g.categoria}</p>
                    <p><strong>Puesto:</strong> ${g.numero_premio}</p>
                    <p><strong>Título:</strong> ${g.titulo_obra}</p>
                    <p><strong>Autor:</strong> ${g.nombre_contacto}</p>
                </div>
            `).join("");

        } catch (err) {
            console.error(err);
            cont.innerHTML = "<p>Error al cargar los ganadores.</p>";
        }
    }

    async function cargarGaleria() {
        const cont = document.getElementById("galeria-imagenes");
        if (!galaActual) return;

        try {
            const res = await fetch(`../php/gala-galeria-listar.php?id_gala=${galaActual.id}`);
            const data = await res.json();

            if (!data.ok || !data.data.length) {
                cont.innerHTML = "<p>No hay imágenes en la galería todavía.</p>";
                return;
            }

            cont.innerHTML = data.data.map(img => `
                <div class="gala-imagen-mini">
                    <img src="../uploads/${img.ruta_imagen}" alt="">
                </div>
            `).join("");

        } catch (err) {
            console.error(err);
            cont.innerHTML = "<p>Error al cargar la galería.</p>";
        }
    }

    async function subirImagenGaleria(e) {
        e.preventDefault();

        const input = document.getElementById("imagen-galeria");
        const error = document.getElementById("galeria-error");
        error.textContent = "";

        if (!input.files || !input.files.length) {
            error.textContent = "Debes seleccionar una imagen.";
            return;
        }

        const datos = new FormData();
        datos.append("id_gala", galaActual.id);
        datos.append("imagen", input.files[0]);

        try {
            const res = await fetch("../php/gala-galeria-subir.php", {
                method: "POST",
                body: datos
            });

            const r = await res.json();

            if (r.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Imagen subida",
                    text: "La imagen se ha añadido a la galería."
                });
                input.value = "";
                cargarGaleria();
            } else {
                error.textContent = r.msg || "No se ha podido subir la imagen.";
            }

        } catch (err) {
            console.error(err);
            error.textContent = "Error al subir la imagen.";
        }
    }

    cargarGala();

});
