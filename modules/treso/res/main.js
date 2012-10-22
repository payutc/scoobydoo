var bygroup = _.groupBy(summary, function (x) { return x.categorie; });

$(function () {

	container = $("#treso");

	table = $("<table>").attr("class", "table table-bordered table-zebra");
	table.append("<tr><th>Categorie</th><th>Produit</th><th>Nombre</th><th>Montant total</th></tr>");
	td = $("<td>");
	tr = $("<tr>");
	
	_.each(bygroup, function (categorie, indexc) {

		_.each(categorie, function (produit, indexp) {
			line = tr.clone();
			if (indexp === 0)
				line.append(td.clone().text(indexc).attr("rowspan", categorie.length + 1));
			
			line.append(td.clone().text(produit.obj_name));
			line.append(td.clone().text(parseFloat(produit.nombre)));
			line.append(td.clone().text(parseFloat(produit.montant_total)));

			table.append(line);
			if (indexp == categorie.length - 1) {
				line = tr.clone().css("background", "red");
				line.append(td.clone().text("Total"));
				line.append(td.clone());
				line.append(td.clone().text(_.reduce(categorie, 
													function (prev, cur) { 
														console.log(cur, prev);
														return parseFloat(cur.montant_total) + prev;
													}, 0)));
				table.append(line);

			}
		});		
	});

	container.append(table);

    $( "#datepicker" ).datepicker({
        showWeek: true,
        firstDay: 1
    });

    $("#datepicker").change(function () {
    	console.log($("#datepicker").attr("value");
    })
    
});
