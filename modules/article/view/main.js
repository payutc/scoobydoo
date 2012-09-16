$(document).ready(function () {

	// GLOBALS
	spinners = {};
	current_node_view = null;
	current_fundation_id = null;
	current_categorie_id = null;

	// activer le plugin dropdown de bootsrap
	$('.dropdown-toggle').dropdown();

	// activer le plugin jqtree
	$('#tree').tree({
        data: [],
		autoOpen: 1,
		//dragAndDrop: true,
		selectable: true,
		/*onCanMove: function(node) {
			if (! node.parent.parent) {
				// Example: Cannot move root node
				return false;
			}
			else {
				return true;
			}
		},
		onCanMoveTo: function(moved_node, target_node, position) {
			if (target_node.is_menu) {
				// Example: can move inside menu, not before or after
				return (position == 'inside');
			}
			else {
				return true;
			}
			return true;
		}*/
		onCreateLi: function(node, $li) {
			// Add 'icon' span before title
			$li.find('div').wrap('<a />');
			$('#tree').find('ul').addClass('nav nav-list');
		}
    });

	// lancer la rafraichissement de l'arbre
    refresh_tree();


	// BINDINGS
    $('#tojson').click(function(event) {
		alert($('#tree').tree('toJson'));
	});
    $('#refresh').click(refresh_tree);
    $('#save_article').click(function(event) {
		event.preventDefault();
		save_article();
	});
	$('#add_article').click(function(event) {
		event.preventDefault();
		clear_article();
		display_article_view();
	});
    $('#delete_article').click(function(event) {
		event.preventDefault();
		delete_article();
	});
    $('#save_categorie').click(function(event) {
		event.preventDefault();
		save_categorie();
	});
	$('#add_categorie').click(function(event) {
		event.preventDefault();
		clear_categorie();
		display_categorie_view();
	});
    $('#delete_categorie').click(function(event) {
		event.preventDefault();
		delete_categorie();
	});
	$('#categorie_field_parent_id').change(function(event) {
		var opt = $('#categorie_field_parent_id option:selected');
		var parent_id = opt.val();
		if (parent_id.indexOf('fun') != -1) {
			$('#categorie_field_fundation_id').val(parent_id.substr(3));
		}
		else {
			var node_categorie = get_nod_by_id(parent_id);
			$('#categorie_field_fundation_id').val(node_categorie.fundation_id);
		}
	});

	/*$('#tree').bind(
		'tree.move',
		function(event) {
			console.log('moved_node', e.move_info.moved_node);
			console.log('target_node', e.move_info.target_node);
			console.log('position', e.move_info.position);
			console.log('previous_parent', e.move_info.previous_parent);
		}
	);*/

	 
	// bind 'tree.click' event
	$('#tree').bind(
		'tree.click',
		function(event) {
			// The clicked node is 'event.node'
			var node = event.node;
			select_node(node);
		}
	);
});

/**
 * Arreter un spinner
 */
function stop_spinner(id) {
	spinners[id].stop();
}

/**
 * Démarrer un spinner
 */
function start_spinner(id) {
	if (spinners[id]) {
		stop_spinner(id);
		spinners[id].spin($(id)[0]);
	}
	else {
		var opts = {
			lines: 13, // The number of lines to draw
			length: 30, // The length of each line
			width: 10, // The line thickness
			radius: 35, // The radius of the inner circle
			rotate: 0, // The rotation offset
			color: '#000', // #rgb or #rrggbb
			speed: 0.8, // Rounds per second
			trail: 81, // Afterglow percentage
			shadow: false, // Whether to render a shadow
			hwaccel: false, // Whether to use hardware acceleration
			className: 'spinner', // The CSS class to assign to the spinner
			zIndex: 2e9, // The z-index (defaults to 2000000000)
			top: 'auto', // Top position relative to parent in px
			left: 'auto' // Left position relative to parent in px
		};
		spinners[id] = new Spinner(opts).spin($(id)[0]);
	}
}

/**
 * Spinner convenience functions
 */
function start_tree_spinner() { start_spinner('#treediv'); }
function start_details_spinner() { start_spinner('#detailsdiv'); }
function stop_tree_spinner() { stop_spinner('#treediv'); }
function stop_details_spinner() { stop_spinner('#detailsdiv'); }


/**
 * Selectionne un node:
 * 	1. change la couleur de fond de la ligne
 * 	2. charge dans les infos dans la vue details
 */
function select_node(node,data) {
	current_node_view = node;
	highlight(node.id);
	if (node.type == 'fundation') {
		if (!data) load_fundation_details(node.id);
		else fill_fundation(data);
	}
	else if (node.type == 'categorie') {
		if (!data) load_categorie_details(node.id);
		else fill_categorie(data);
	}
	else {
		if (!data) load_article_details(node.id);
		else fill_article(data);
	}
}

/**
 * Convenience function
 * @param id
 */
function select_node_by_id(id,data) {
	var node = get_nod_by_id(id);
	select_node(node,data);
}

/**
 * Récupérer un node de l'arbre à partir de son id
 */
function get_nod_by_id(id) {
	return $('#tree').tree('getNodeById',id);
}

/**
 * Ajouter un node à l'arbre
 * @parem {Node} parent
 * @parem id
 * @param name
 */
function append_node(parent, id, name) {
	$('#tree').tree('appendNode',
		{ id: id, name: name },
		parent
	);
}

function update_node(id, name) {
	refresh_tree();
	// TODO
	/*node = get_nod_by_id(id);
	t2 = node;
	parent = node.parent;
	children = node.children;
	$('#tree').tree('removeNode', node);
	node.name = name;
	$('#tree').tree('appendNode', node, parent);
	for (var i in children) {
		node = get_nod_by_id(id);
		$('#tree').tree('appendNode', children[i], node);
	}
	data = jQuery.parseJSON( $('#tree').tree('toJson') );
	$('#tree').tree('loadData', data);
	*/
}

/**
 * Cacher toutes les vues
 */
function hide_all_views() {
	$('#article_details').hide();
	$('#categorie_details').hide();
	$('#fundation_details').hide();
	$('#home').hide();
}

/**
 * Afficher la vue des articles et cacher les autres
 */
function display_article_view() {
	hide_all_views();
	$('#article_details').show();
}

/**
 * Afficher la vue des catégories et cacher les autres
 */
function display_categorie_view() {
	hide_all_views();
	$('#categorie_details').show();
}

/**
 * Afficher la vue des assos et cacher les autres
 */
function display_fundation_view() {
	hide_all_views();
	$('#fundation_details').show();
}

/**
 * Vider les champs du formulaire article. Pré remplie catégorie avec la
 * catégorie courante.
 */
function clear_article() {
	var data = {
		id: '',
		name: 'Nouvel article',
		price: 0,
		stock: 0,
		categorie_id: current_categorie_id,
	}
	fill_article(data);
}

/**
 * Vider les champs du formulaire article. Préremplie parent et asso avec
 * les variables globales courantes
 */
function clear_categorie() {
	var data = {
		id: '',
		name: 'Nouvelle catégorie',
		categorie_id: current_fundation_id,
		fundation_id: current_fundation_id
	}
	fill_categorie(data);
}

/**
 * Remplir la vue article.
 * @data {id, name, price, stock, categorie_id} data donées récupérée via ajax 
 */
function fill_article(data) {
	var price = data.price;
	if (price) {
		price /= 100.0;
		price = (''+price).replace('.', ',');
	}
	
	$('#article_name').html(data.name);
	$('#article_id').html(data.id);
	$('#article_field_name').val(data.name);
	$('#article_field_price').val(price);
	$('#article_field_stock').val(data.stock);
	$('#article_field_alcool').attr("checked", data.alcool ? "checked" : "");	
	if (data.categorie_id) $('#article_field_categorie_id').val(data.categorie_id);
}

/**
 * Remplir la vue catégorie.
 * @data {id, name, parent_id} data donées récupérée via ajax
 */
function fill_categorie(data) {
	$('#categorie_name').html(data.name);
	$('#categorie_id').html(data.id);
	$('#categorie_field_name').val(data.name);
	if (data.fundation_id) $('#categorie_field_fundation_id').val(data.fundation_id);
	if (data.parent_id) {
		$('#categorie_field_parent_id').val(data.parent_id);
	}
	else if (data.fundation_id) {
		$('#categorie_field_parent_id').val('fun'+data.fundation_id);
	}
}

/**
 * Remplir la vue fondation.
 * @data {id, name, {array} categories} data donées récupérée via ajax
 */
function fill_fundation(data) {
	$('#fundation_name').html(data.name);
	$('#fundation_id').html(data.id);
	if (data.categories) {
		var html = '';
		for(var i in data.categories) {
			var categorie = data.categories[i];
			html += '<tr><td>'+categorie.id+'</td><td><a>'+categorie.name+'</a></td></tr>';
		}
		$('#fundation_categories').html(html);
	}
}

/**
 * Empackter le formulaire article
 * @return {id, name, price, stock, categorie_id} data
 */
function collect_article_data() {
	var price = $('#article_field_price').val();
	if (price.indexOf(',') != -1)
		price = price.replace(',','.');
	price *= 100;
	
	var data = {
		id : $('#article_id').html(),
		name: $('#article_field_name').val(),
		price: price,
		stock: $('#article_field_stock').val(),
		categorie_id: $('#article_field_categorie_id').val(),
		alcool: $('#article_field_alcool').attr("checked") === "checked" ? 1 : 0,
	};

	return data;
}

/**
 * Empackter le formulaire catégorie
 * @return {id, name, parent_id} data
 */
function collect_categorie_data() {
	var data = {
		id : $('#categorie_id').html(),
		name: $('#categorie_field_name').val(),
		parent_id: $('#categorie_field_parent_id').val(),
	};

	return data;
}

/**
 * Récupère les données de l'arbre via ajax puis l'affiche.
 * Si un id est précisé, le node correspondant sera selectionné.
 * @param {optional} id
 */
function refresh_tree(id,data) {
	start_tree_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("get_tree") ?>',
		async: true,
		success: function(result) {
			$('#tree').tree('loadData', result);
			stop_tree_spinner();
			if (id) {
				select_node_by_id(id,data);
			}
		},
	});
}

/**
 * Surligner un noeud, désurligne l'ancien
 * @param id
 */
function highlight(id) {
	$('#tree').find('.active').removeClass('active');
	var node = $('#tree').tree('getNodeById',id);
	node.element.className = 'active';
}

/**
 * callback lorsqu'un erreur intervient dans une requête ajax des vues.
 */
function on_ajax_error(jqXHR, textStatus, errorThrown) {
	stop_details_spinner();
	var err_msg = ''
	if (jqXHR.status == 200) {
		err_msg = 'La transaction s\'est bien déroulée, cependant une erreur sur la page est survenue. ';
	}

	err_msg += 'StatusCode : '+jqXHR.status;
	err_msg +=' responseText: '+jqXHR.responseText;

	show_alert_error(err_msg);
}

/**
 * Récupérer les infos d'un article via ajax et les charger dans la vue.
 */
function load_article_details(id) {
	var node = get_nod_by_id(id);
	// update globals
	current_node_view = node;
	current_categorie_id = node.parent.id;
	
	fill_article({id: id, name: node.name});
	display_article_view();
	start_details_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("details_article") ?>',
		data: {id: id},
		async: true,
		success: function(result) {
			// arret du spinner
			if (current_node_view.id == node.id) {
				stop_details_spinner();
			}
			if (result.success) {
				// test si on est encore entrain de regarder ce node
				if (current_node_view.id == node.id) {
					fill_article(result.success);
				}
			}
			else {
				show_alert_error(result.error,result.error_msg);
			}
		},
		error: on_ajax_error,
	});
}

/**
 * Récupérer les infos d'une catégorie via ajax et les charger dans la vue.
 */
function load_categorie_details(id) {
	var node = get_nod_by_id(id);
	// update globals
	current_node_view = node;
	current_categorie_id = node.id;
	current_fundation_id = node.parent.id;
	
	fill_categorie({id: id, name: node.name});
	display_categorie_view();
	start_details_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("details_categorie") ?>',
		data: {id: id},
		async: true,
		success: function(result) {
			// arret du spinner
			if (current_node_view.id == node.id) {
				stop_details_spinner();
			}
			if (result.success) {
				// test si on est encore entrain de regarder ce node
				if (current_node_view.id == node.id) {
					fill_categorie(result.success);
				}
			}
			else {
				show_alert_error(result.error,result.error_msg);
			}
		},
		error: on_ajax_error,
	});
}

/**
 * Récupérer les infos d'une fundation via ajax et les charger dans la vue.
 * Lance le spinner.
 */
function load_fundation_details(id) {
	var node = get_nod_by_id(id);
	// update globals
	current_node_view = node;
	current_fundation_id = node.id;
	
	fill_fundation({id: id, name: node.name});
	display_fundation_view();
	start_details_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("details_fundation") ?>',
		data: {id: id},
		async: true,
		success: function(result) {
			// arret du spinner
			if (current_node_view.id == node.id) {
				stop_details_spinner();
			}
			if (result.success) {
				// test si on est encore entrain de regarder ce node
				if (current_node_view.id == node.id) {
					fill_fundation(result.success);
				}
			}
			else {
				show_alert_error(result.error,result.error_msg);
			}
		},
		error: on_ajax_error,
	});
}

/**
 * Sauvegarder l'article.
 * Lance le spinner.
 */
function save_article() {
	close_alert();
	var data = collect_article_data();
	start_details_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("save_article") ?>',
		data: data,
		async: true,
		success: on_save_success(data,fill_article),
		error: on_ajax_error,
	});
}

/**
 * Sauvegarder la catégorie.
 * Lance le spinner.
 */
function save_categorie() {
	close_alert();
	var data = collect_categorie_data();
	start_details_spinner();
	$.ajax({
		url: '<?php echo $this->get_param("save_categorie") ?>',
		data: data,
		async: true,
		success: on_save_success(data,fill_categorie),
		error: on_ajax_error,
	});
}

/**
 * callback lors du succes d'une sauvegarde.
 * Stop le spinner, refresh le tree et affiche un message d'alert.
 */
function on_save_success(data, fill_fn) {
	return function(result) {
		stop_details_spinner();
		if (result.success) {
			// refresh tree, sans redemander au serveur les infos
			data.id = result.success;
			refresh_tree(result.success,data);
			fill_fn(data);

			// affiche le message de succès
			show_alert_success();
		}
		else {
			show_alert_error(result.error,result.error_msg);
		}
	};
}

/**
 * Supprimer un article.
 * Lance et arrête le spinner.
 */
function delete_article() {
	start_details_spinner();
	var data = {id: $('#article_id').html()};
	$.ajax({
		url: '<?php echo $this->get_param("delete_article") ?>',
		data: data,
		async: true,
		success: function(result) {
			stop_details_spinner();
			if (result.success) {
				show_alert_success();
				var node = get_nod_by_id(data.id);
				var parent = node.parent;
				$('#tree').tree('removeNode', node);
				select_node(parent);
			}
			else {
				show_alert_error(result.error,result.error_msg);
			}
		},
		error: on_ajax_error,
	});
}

/**
 * Supprimer une catégorie.
 * Lance et arrête le spinner.
 */
function delete_categorie() {
	start_details_spinner();
	var data = {id: $('#categorie_id').html()};
	$.ajax({
		url: '<?php echo $this->get_param("delete_categorie") ?>',
		data: data,
		async: true,
		success: function(result) {
			stop_details_spinner();
			if (result.success) {
				show_alert_success();
				var node = get_nod_by_id(data.id);
				var parent = node.parent;
				$('#tree').tree('removeNode', node);
				select_node(parent);
			}
			else {
				show_alert_error(result.error,result.error_msg);
			}
		},
		error: on_ajax_error,
	});
}

/**
 * Afficher un message vert pour dire que succès il y a eu.
 */
function show_alert_success() {
	$('#alert').html(
		'<div class="alert alert-success">'+
		'	<button type="button" class="close" data-dismiss="alert">×</button>'+
		'	<strong>Félicitation !</strong> Sans embrouilles à ta fin tu es arrivé.'+
		'</div>'
	);
	bind_close_alert();
}

/**
 * Afficher un message d'erreur
 */
function show_alert_error(err_code,err_msg) {
	$('#alert').html(
		'<div class="alert alert-error">'+
		'	<button type="button" class="close" data-dismiss="alert">×</button>'+
		'	<strong>Crotte de biquette !</strong> Il y a eu une couille.<br/>'+
		'	Code d\'erreur : <strong>'+err_code+'</strong>. '+err_msg+
		'</div>'
	);
	bind_close_alert();
}

/**
 * Tool function, permet de binder le bouton close de l'alert créer à la suppressiond e celle ci.
 */
function bind_close_alert() {
	$('.close').click(function(event) {
		close_alert();
	});
}

function close_alert() {
	$('#alert').html('');
}
