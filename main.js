
$(document).ready(function () {

	$('#tree1').tree({
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
    });

    refresh_tree();

    $('#tojson').click(function(event) {
		alert($('#tree1').tree('toJson'));
	});

    $('#refresh').click(function(event) {
		refresh_tree();
	});

	/*$('#tree1').bind(
		'tree.move',
		function(event) {
			console.log('moved_node', e.move_info.moved_node);
			console.log('target_node', e.move_info.target_node);
			console.log('position', e.move_info.position);
			console.log('previous_parent', e.move_info.previous_parent);
		}
	);*/

	 
	// bind 'tree.click' event
	$('#tree1').bind(
		'tree.click',
		function(event) {
			// The clicked node is 'event.node'
			var node = event.node;
			load_article_details(node.id);
		}
	);
});

function refresh_tree() {
	$.ajax({
		url: 'http://localhost/scoobydoo/?module=article&ajax=get_tree',
		async: true,
		success: function(data) {
			$('#tree1').tree('loadData', data);
		},
	});
}

function load_article_details(id) {
	$.ajax({
		url: 'http://localhost/scoobydoo/?module=article&ajax=details_article',
		data: {id: id},
		async: true,
		success: function(data) {
			$('#article_name').html(data.name);
			$('#article_id').html(data.id);
			$('#article_field_name').val(data.name);
			$('#article_field_price').val(data.price);
		},
	});
}

