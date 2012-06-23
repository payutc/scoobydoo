$(document).ready(function () {

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

    refresh_tree();

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
		$('#article_name').html('');
		$('#article_id').html('');
		$('#article_field_price').val('');
		$('#article_field_name').val('');
		$('#article_field_stock').val('');
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
		$('#categorie_name').html('');
		$('#categorie_id').html('');
		$('#categorie_field_name').val('');
	});
    $('#delete_categorie').click(function(event) {
		event.preventDefault();
		delete_categorie();
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

function select_node(node) {
	highlight(node.id);
	if (node.type == 'fundation') {
		load_fundation_details(node.id);
	}
	else if (node.type == 'categorie') {
		load_categorie_details(node.id);
	}
	else {
		load_article_details(node.id);
	}
}

function get_nod_by_id(id) {
	return $('#tree').tree('getNodeById',id);
}

/**
 * Afficher la vue des articles et cacher les autres
 */
function display_article_view() {
	$('#article_details').show();
	$('#categorie_details').hide();
}

/**
 * Afficher la vue des catégories et cacher les autres
 */
function display_categorie_view() {
	$('#article_details').hide();
	$('#categorie_details').show();
}

function fill_article(data) {
	$('#article_name').html(data.name);
	$('#article_id').html(data.id);
	$('#article_field_name').val(data.name);
	$('#article_field_price').val(data.price);
	$('#article_field_stock').val(data.stock);
	$('#article_field_categorie_id').val(data.parent_id);
}

function fill_categorie(data) {
	$('#categorie_name').html(data.name);
	$('#categorie_id').html(data.id);
	$('#categorie_field_name').val(data.name);
	$('#categorie_field_fundation_id').val(data.fundation_id);
	if (data.parent_id) {
		$('#categorie_field_parent_id').val(data.parent_id);
	}
	else {
		$('#categorie_field_parent_id').val('fun'+data.fundation_id);
	}
}

function refresh_tree() {
	$.ajax({
		url: '<?=$this->get_param("get_tree")?>',
		async: true,
		success: function(data) {
			$('#tree').tree('loadData', data);
		},
	});
}

function highlight(id) {
	$('#tree').find('.active').removeClass('active');
	var node = $('#tree').tree('getNodeById',id);
	node.element.className = 'active';
}

function load_article_details(id) {
	var node = get_nod_by_id(id);
	fill_article({id: id, name: node.name});
	display_article_view();
	$.ajax({
		url: '<?=$this->get_param("details_article")?>',
		data: {id: id},
		async: true,
		success: function(result) {
			if (result.success) {
				fill_article(result.success);
				display_article_view();
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		},
	});
}

function load_categorie_details(id) {
	var node = get_nod_by_id(id);
	fill_categorie({id: id, name: node.name});
	display_categorie_view();
	$.ajax({
		url: '<?=$this->get_param("details_categorie")?>',
		data: {id: id},
		async: true,
		success: function(result) {
			if (result.success) {
				fill_categorie(result.success);
				display_categorie_view();
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		},
	});
}

function load_fundation_details(id) {
	alert('todo');
}

function save_article() {
	close_alert();
	var data = {
			id: $('#article_id').html(),
			name: $('#article_field_name').val(),
			categorie_id: $('#article_field_categorie_id').val(),
			price: $('#article_field_price').val(),
			stock: $('#article_field_stock').val(),
	};
	
	$.ajax({
		url: '<?=$this->get_param("save_article")?>',
		data: data,
		async: true,
		success: function(result) {
			if (result.success) {
				show_alert_success();
				// si ajout, on ajoute à l'arbre
				if (!data.id) {
					var parent = $('#tree').tree('getNodeById',data.categorie_id);
					$('#tree').tree('appendNode',
						{
							name: data.name,
							id: result.success,
						},
						parent
					);
				}
				// si update, on update l'arbre
				else {
					var node = $('#tree').tree('getNodeById',data.id);
					var parent = node.parent;
					node.name = data.name;
					$('#tree').tree('removeNode', node);
					$('#tree').tree('appendNode', node, parent);
				}

				// surligner la bonne ligne
				highlight(result.success);

				// afficher le message
				load_article_details(result.success);
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			error = [jqXHR, textStatus, errorThrown];
		},
	});
}

function save_categorie() {
	close_alert();
	data = {
		id: $('#categorie_field_id').val(),
		name: $('#categorie_field_name').val(),
		parent_id: $('#categorie_field_parent_id').val(),
	};
	$.ajax({
		url: '<?=$this->get_param("save_categorie")?>',
		data: data,
		async: true,
		success: function(result) {
			if (result.success) {
				show_alert_success();
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		},
	});
}

function delete_article() {
	var data = {id: $('#article_id').html()};
	$.ajax({
		url: '<?=$this->get_param("delete_article")?>',
		data: data,
		async: true,
		success: function(result) {
			if (result.success) {
				show_alert_success();
				var node = get_nod_by_id(data.id);
				var parent = node.parent;
				$('#tree').tree('removeNode', node);
				select_node(parent);
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		}
	});
}

function delete_categorie() {
	var data = {id: $('#categorie_id').html()};
	$.ajax({
		url: '<?=$this->get_param("delete_categorie")?>',
		data: data,
		async: true,
		success: function(result) {
			if (result.success) {
				show_alert_success();
				var node = get_nod_by_id(data.id);
				var parent = node.parent;
				$('#tree').tree('removeNode', node);
				select_node(parent);
			}
			else {
				show_alert_fail(result.error+' '+result.error_msg);
			}
		}
	});
}

function show_alert_success() {
	$('#alert').html(
		'<div class="alert alert-success">'+
		'	<button type="button" class="close" data-dismiss="alert">×</button>'+
		'	<strong>Well done!</strong> You successfully read this important alert message.'+
		'</div>'
	);
	bind_close_alert();
}

function show_alert_fail(msg) {
	$('#alert').html(
		'<div class="alert alert-error">'+
		'	<button type="button" class="close" data-dismiss="alert">×</button>'+
		'	<strong>Oh snap!</strong> Change a few things up and try submitting again.'+
		'	Err : '+msg+
		'</div>'
	);
	bind_close_alert();
}

function bind_close_alert() {
	$('.close').click(function(event) {
		close_alert();
	});
}

function close_alert() {
	$('#alert').html('');
}
