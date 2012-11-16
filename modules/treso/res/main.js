var bygroup = _.groupBy(summary, function (x) { return x.categorie; }) || [];

$(function () {

    container = $("#treso");

    table = $("<table>").attr("class", "table table-bordered table-zebra");
    table.append("<tr><th>Categorie</th><th>Produit</th><th>Nombre</th><th>Montant total</th></tr>");
    td = $("<td>");
    tr = $("<tr>");
    
    _.each(bygroup, function (categorie, indexc) {
        _.each(categorie, function (produit, indexp) {
            var line = tr.clone();
            if (indexp === 0)
                line.append(td.clone().text(indexc).attr("rowspan", categorie.length + 1));
            
            var montant_total = Math.round(parseFloat(produit.montant_total) * 100) / 100
            line.append(td.clone().text(produit.obj_name));
            line.append(td.clone().text(parseInt(produit.nombre)).css("text-align", "right"));
            line.append(td.clone().text(montant_total + " €").css("text-align", "right"));

            table.append(line);
            if (indexp == categorie.length - 1) {
                line = tr.clone();
                line.append(td.clone().text("Total"));
                line.append(td.clone());
                line.append(td.clone()
                              .text(Math.round(_.reduce(categorie,
                                            function (prev, cur) {
                                                return parseFloat(cur.montant_total) + prev;
                                            }, 0) * 100) / 100 + " €")
                              .css("text-align", "right"));
                table.append(line);

            }
        });
    });
    var total_sum = Math.round(_.reduce(summary, function (prev, cur) {
                                                return parseFloat(cur.montant_total) + prev;
                                            }, 0) * 100) / 100 + " €";
    var line = tr.clone().append("<td>Total</td><td colspan=4 class='number' style='text-align:right'>" + total_sum + "</td>");

    table.append(line);

    container.append(table);

    $( "#datepicker, #datepicker2" ).datepicker({
        showWeek: true,
        firstDay: 1,
        dateFormat : "dd/mm/yy"
    });
    var now = new Date();
    $( "#datepicker, #datepicker2" ).attr("value", now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear())



    $("#dates").submit(function () {
        var date = $("#datepicker").attr("value").split("/");

        var date2 = undefined
        if ($("#datepicker2").attr("value") != "" || $("#datepicker2").attr("value") != $("#datepicker").attr("value"))
            date2 = $("#datepicker2").attr("value").split("/");

        if (date2) 
        window.location = ("?module=treso&action=index&day=" + date[0] + "&month=" + date[1] + "&year=" + date[2]
                            + "&day2=" + date2[0] + "&month2=" + date2[1] + "&year2=" + date2[2]);
        return false;
    });
    
});
