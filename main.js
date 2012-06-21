
$(document).ready(function () {
	var data = [
		{
			id: 1,
			label: 'node1',
			children: [
				{ id: 2, label: 'child1' },
				{ id: 3, label: 'child2' }
			]
		},
		{
			id: 4,
			label: 'node2',
			children: [
				{ id: 5, label: 'child3' }
			]
		}
	];

	$('#tree1').tree({
        data: data,
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

    $('#tojson').click(function(event) {
		alert($('#tree1').tree('toJson'));
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
			alert(node.name);
		}
	);
});
