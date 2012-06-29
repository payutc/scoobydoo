function init() {
  // Instanciate sigma.js and customize it :
  var sigInst = sigma.init(document.getElementById('sigma-zone')).drawingProperties({
    defaultLabelColor: '#fff',
    defaultEdgeType: 'curve'
  });
 
  // ADD NODES USERS
  var user_color = 'rgb('+Math.round(0*256)+','+
                      Math.round(0*256)+','+
                      Math.round(1*256)+')';

  for(var key in users)
  {
    var user = users[key];
    sigInst.addNode('user_'+user['user_id'],{
      'x': Math.random(),
      'y': Math.random(),
      'size': 4,
      'attributes': [{attr: 'Prénom', val: user['usr_firstname']},{attr: 'Nom', val: user['usr_lastname']}],
      'color': user_color
    });
  }

  // ADD NODES PRODUCTS
  var article_color = 'rgb('+Math.round(1*256)+','+
                      Math.round(0*256)+','+
                      Math.round(0*256)+')';

  for(var key in articles)
  {
    var article = articles[key];
    sigInst.addNode('article_'+article['id'],{
      'x': Math.random(),
      'y': Math.random(),
      'size': 4,
      'attributes': [{attr: 'Nom', val: article['name']},{attr: 'Prix', val: article['price']/100+' €'}],
      'color': article_color
    });
  } 

  // ADD EDGES
  var edge_id = 0;
  for(var key in transactions)
  {
    var transaction = transactions[key];
    sigInst.addEdge(edge_id,'user_'+transaction['usr_id_buyer'],'article_'+transaction['obj_id']);
    edge_id++;
  }
 
  /**
   * Now, here is the code that shows the popup :
   */
  (function(){
    var popUp;
 
    // This function is used to generate the attributes list from the node attributes.
    // Since the graph comes from GEXF, the attibutes look like:
    // [
    //   { attr: 'Lorem', val: '42' },
    //   { attr: 'Ipsum', val: 'dolores' },
    //   ...
    //   { attr: 'Sit',   val: 'amet' }
    // ]
    function attributesToString(attr) {
      return '' +
        attr.map(function(o){
          return '<li>' + o.attr + ' : ' + o.val + '</li>';
        }).join('') +
        '';
    }
 
    function showNodeInfo(event) {
      popUp && popUp.remove();
 
      var node;
      sigInst.iterNodes(function(n){
        node = n;
      },[event.content[0]]);
 

      popUp = $(
        '<div class="node-info-popup"></div>'
      ).append(
        // The GEXF parser stores all the attributes in an array named
        // 'attributes'. And since sigma.js does not recognize the key
        // 'attributes' (unlike the keys 'label', 'color', 'size' etc),
        // it stores it in the node 'attr' object :
        attributesToString( node['attr']['attributes'] )
      ).attr(
        'id',
        'node-info'+sigInst.getID()
      ).css({
        'display': 'inline-block',
        'border-radius': 3,
        'padding': 5,
        'background': '#fff',
        'color': '#000',
        'box-shadow': '0 0 4px #666',
        'position': 'relative',
        'left': node.displayX,
        'top': node.displayY+15
      });
      $('ul',popUp).css('margin','0 0 0 20px');
      $('#sigma-zone').append(popUp);
    }
 
    function hideNodeInfo(event) {
      popUp && popUp.remove();
      popUp = false;
    }
 
    sigInst.bind('overnodes',showNodeInfo).bind('outnodes',hideNodeInfo).draw();
  })();


  // Start the ForceAtlas2 algorithm
  // (requires "sigma.forceatlas2.js" to be included)
  sigInst.startForceAtlas2();
 
  var isRunning = true;
  document.getElementById('stop-layout').addEventListener('click',function(){
    if(isRunning){
      isRunning = false;
      sigInst.stopForceAtlas2();
      document.getElementById('stop-layout').childNodes[0].nodeValue = 'Start Layout';
    }else{
      isRunning = true;
      sigInst.startForceAtlas2();
      document.getElementById('stop-layout').childNodes[0].nodeValue = 'Stop Layout';
    }
  },true);
  document.getElementById('rescale-graph').addEventListener('click',function(){
    sigInst.position(0,0,1).draw();
  },true);
}
 
if (document.addEventListener) {
  document.addEventListener('DOMContentLoaded', init, false);
} else {
  window.onload = init;
}