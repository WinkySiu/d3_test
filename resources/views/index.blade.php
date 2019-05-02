<!DOCTYPE html>
<meta charset="utf-8">
<style>

/*--------- 線的顏色與透明度 ---------*/
.links line {
  stroke: #999;
  stroke-opacity: 0.6;
}

/*--------- Node的邊顏色與寬度 ---------*/
.nodes circle {
  stroke: #fff;
  stroke-width: 0px;
}

/*--------- Node的文字大小與字型 ---------*/
text {
  font-family: sans-serif;
  font-size: 10px;
}

</style>
<svg width="1000" height="700"></svg>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script>

var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height");

var color = d3.scaleOrdinal(d3.schemeCategory20);

var simulation = d3.forceSimulation()
    .force("link", d3.forceLink().id(function(d) { return d.id; }).distance(100).strength(0.1))
    .force("charge", d3.forceManyBody().strength(-5))
    .force("center", d3.forceCenter(width / 2, height / 1.7));

d3.json("./result.json", function(error, graph) {
  if (error) throw error;

  var link = svg.append("g")
    .attr("class", "links")
    .selectAll("line")
    .data(graph.links)
    .enter().append("line")
    .attr("stroke-width", function(d) { return Math.sqrt(d.value); });

  var node = svg.append("g")
    .attr("class", "nodes")
    .selectAll("g")
    .data(graph.nodes)
    .enter().append("g")

/*--------- Node大小與Event ---------*/
  var circles = node.append("circle")
      .attr("r", function(d) {
        r = 5;
        if (d.id == "nor_level-6_IBD"){
          r = 20;
        } else if (d.id.split(" ").length == 2) {
          r = 10
        }

        return r;
      })
      .attr("fill", function(d) { return color(d.group); })
      .call(d3.drag()
          .on("start", dragstarted)
          .on("drag", dragged));
          // .on("end", dragended));

/*--------- Node文字位置 ---------*/
  var lables = node.append("text")
      .text(function(d) {
        // Node的文字
        var name = d.id;
        var str_split = name.split(' ');
        if (str_split.length > 2) {
          name = str_split[2];
        }
        return name;
      })
      .attr('x', function(d) {
        x = 6;
        if (d.id == "nor_level-6_IBD"){
          x = 21;
        } else if (d.id.split(" ").length == 2) {
          x = 11;
        }

        return x;
      })
      .attr('y', 3);

  node.append("title")
      .text(function(d) { return d.id; });

  simulation
      .nodes(graph.nodes)
      .on("tick", ticked);

  simulation.force("link")
      .links(graph.links);

  function ticked() {
    link
        .attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node
        .attr("transform", function(d) {
          return "translate(" + d.x + "," + d.y + ")";
        })
  }
});

function dragstarted(d) {
  if (!d3.event.active) simulation.alphaTarget(0.1).restart();
  d.fx = d.x;
  d.fy = d.y;
}

function dragged(d) {
  d.fx = d3.event.x;
  d.fy = d3.event.y;
}

function dragended(d) {
  if (!d3.event.active) simulation.alphaTarget(0);
  d.fx = null;
  d.fy = null;
}

</script>
