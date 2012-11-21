var bygroup = _.groupBy(summary, function (x) { return x.categorie; }) || [];

var format_money = function (money) {
    // money is given in cents as a string
    // we want it only with 2 decimals and in euro

    return Math.round(parseFloat(money)) / 100;
}

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

            // the second 'divide by 100' is because all the amounts server-side are in cents
            var montant_total = format_money(produit.montant_total);
            line.append(td.clone().text(produit.obj_name));
            line.append(td.clone().text(parseInt(produit.nombre)).css("text-align", "right"));
            line.append(td.clone().text(montant_total + " €").css("text-align", "right"));

            table.append(line);
            if (indexp == categorie.length - 1) {
                line = tr.clone();
                line.append(td.clone().text("Total"));
                line.append(td.clone());
                var total = _.reduce(categorie,
                                            function (prev, cur) {
                                                return format_money(cur.montant_total) + prev;
                                            }, 0);
                line.append(td.clone()
                              .text(total.toFixed(2) + " €")
                              .css("text-align", "right"));
                table.append(line);

            }
        });
    });
    var total_sum = _.reduce(summary, function (prev, cur) {
                                        return format_money(cur.montant_total) + prev;
                                            }, 0);
    var line = tr.clone().append("<td>Total</td>");
    line.append("<td colspan=4 class='number' style='text-align:right'>" + total_sum.toFixed(2) + " €" + "</td>");

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
