<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Hello world</title>
</head>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>


<script type="text/javascript">
function saveAspdf() {
var pdf = new jsPDF('p','pt','a4');
pdf.addHTML(document.body,function() {
    pdf.save('web.pdf');
});
}
</script>


<body>
    <p id="to-pdf">Generating PDF</p>
</body>

</html>



<input type="button" value="save" onclick="javascript:saveAspdf()"/>