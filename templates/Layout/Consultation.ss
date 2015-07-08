<h1>$Category.Title > $Title</h1>

<h3>Rank: #$GlobalPopularity</h3>
<% if PopularityInCategory %><h3>Rank in this category: #$PopularityInCategory</h3><% end_if %>

$Content

<% loop AllReports %>
	$Me.render()
<% end_loop %>

<% loop Submissions %>
	<hr/>
	<% include ConsultationComment %>
<% end_loop %>
