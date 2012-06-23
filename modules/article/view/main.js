$(document).ready(function () {

	$('#tree').tree({
        data: [],
		autoOpen: true,
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
		$('#input_field_price').val('');
		$('#input_field_name').val('');
		$('#input_field_stock').val('');
	});
    $('#save_categorie').click(function(event) {
		event.preventDefault();
		save_categorie();
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
			$('#tree').find('.active').removeClass('active');
			// The clicked node is 'event.node'
			var node = event.node;
			node.element.className = 'active';
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
	);
});

function refresh_tree() {
	$.ajax({
		url: '<?=$this->get_param("get_tree")?>',
		async: true,
		success: function(data) {
			$('#tree').tree('loadData', data);
		},
	});
}

function load_article_details(id) {
	$.ajax({
		url: '<?=$this->get_param("details_article")?>',
		data: {id: id},
		async: true,
		success: function(data) {
			t = data;
			$('#article_name').html(data.name);
			$('#article_id').html(data.id);
			$('#article_field_name').val(data.name);
			$('#article_field_price').val(data.price);
			$('#article_field_stock').val(data.stock);
			$('#article_field_categorie_id').val(data.parent_id);
			$('#article_details').show();
			$('#categorie_details').hide();
		},
	});
}

function load_categorie_details(id) {
	$.ajax({
		url: '<?=$this->get_param("details_categorie")?>',
		data: {id: id},
		async: true,
		success: function(data) {
			t = data;
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
			$('#article_details').hide();
			$('#categorie_details').show();
		},
	});
}

function load_fundation_details(id) {
	alert('todo');
}

function save_article() {
	$.ajax({
		url: '<?=$this->get_param("save_article")?>',
		data: {
			id: $('#article_field_name').val(),
			categorie_id: $('#article_field_categorie_id').val(),
			price: $('#article_field_price').val(),
		},
		async: true,
		success: function(data) {
			if (data.success == 'ok') {
				show_alert_success();
			}
			else {
				show_alert_fail();
			}
		},
	});
}

function save_categorie() {
	$.ajax({
		url: '<?=$this->get_param("save_categorie")?>',
		data: {
			id: $('#categorie_field_name').val(),
			parent_id: $('#categorie_field_parent_id').val(),
		},
		async: true,
		success: function(data) {
			if (data.success == 'ok') {
				show_alert_success();
			}
			else {
				show_alert_fail();
			}
		},
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

function show_alert_fail() {
	$('#alert').html(
		'<div class="alert alert-error">'+
		'	<button type="button" class="close" data-dismiss="alert">×</button>'+
		'	<strong>Oh snap!</strong> Change a few things up and try submitting again.'+
		'</div>'
	);
	bind_close_alert();
}

function bind_close_alert() {
	$('.close').click(function(event) {
		$('#alert').html('');
	});
}
