/* ============================================================
   Grafica de barras: libros por estado.
   Los tres numeros llegan desde PHP en los atributos data-* del canvas.
   ============================================================ */

// Se busca el canvas en la pagina
var canvas = document.getElementById("graficaEstados");

// Si el canvas no existe no se hace nada, por ejemplo en otra pagina
if (canvas) {

    // Se leen los numeros que PHP escribio en data-pendiente, data-leyendo y data-terminado.
    // Number() convierte el texto a numero y si no puede, deja 0
    var pendiente = Number(canvas.dataset.pendiente) || 0;
    var leyendo   = Number(canvas.dataset.leyendo)   || 0;
    var terminado = Number(canvas.dataset.terminado) || 0;

    // Tres arreglos paralelos: la posición 0 es "Pendiente", la 1 "Leyendo" y la 2 "Terminado"
    var nombres = ["Pendiente", "Leyendo", "Terminado"];
    var valores = [pendiente, leyendo, terminado];
    var colores = ["#B7906A", "#8C2A38", "#241B19"];

    // El valor mas alto sirve para calcular la altura de las barras.
    // Se empieza en 1 para que nunca se divida entre cero
    var maximo = 1;
    if (valores[0] > maximo) { maximo = valores[0]; }
    if (valores[1] > maximo) { maximo = valores[1]; }
    if (valores[2] > maximo) { maximo = valores[2]; }

    // Se prepara el lienzo y se guardan su ancho y su alto
    var ctx = canvas.getContext("2d");
    var ancho = canvas.width;   // 420
    var alto  = canvas.height;  // 260

    // Margenes para que las barras no queden pegadas a los bordes
    var margenInferior = 40;
    var margenSuperior = 30;
    var margenLateral  = 20;
    var altoUtil  = alto - margenInferior - margenSuperior;
    var anchoUtil = ancho - margenLateral * 2;

    // El ancho util se reparte entre las tres barras
    var anchoGrupo = anchoUtil / 3;
    var anchoBarra = anchoGrupo * 0.5;

    // Se dibuja la linea de abajo, que es el eje X
    var yBase = alto - margenInferior;
    ctx.strokeStyle = "#DED3C2";
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(margenLateral, yBase);
    ctx.lineTo(ancho - margenLateral, yBase);
    ctx.stroke();

    // Un ciclo dibuja las tres barras, una por una
    var i;
    for (i = 0; i < 3; i++) {

        // Centro horizontal de la barra que toca dibujar
        var centro = margenLateral + anchoGrupo * i + anchoGrupo / 2;

        // La altura es proporcional al valor, comparado con el maximo
        var altura = (valores[i] / maximo) * altoUtil;

        // Esquina superior izquierda del rectangulo
        var x = centro - anchoBarra / 2;
        var y = yBase - altura;

        // Se pinta la barra
        ctx.fillStyle = colores[i];
        ctx.fillRect(x, y, anchoBarra, altura);

        // El numero va arriba de la barra
        ctx.fillStyle = "#2B2422";
        ctx.font = "bold 15px Georgia, serif";
        ctx.textAlign = "center";
        ctx.fillText(String(valores[i]), centro, y - 8);

        // El nombre va debajo, junto al eje X
        ctx.fillStyle = "#7A6E65";
        ctx.font = "12px Segoe UI, sans-serif";
        ctx.fillText(nombres[i], centro, alto - margenInferior + 22);
    }
}
