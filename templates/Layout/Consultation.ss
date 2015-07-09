<h1>$Breadcrumbs</h1>

<section class="consultation-ranking">
	<h3>Rank: #$GlobalPopularity</h3>
	<% if PopularityInCategory %><h3>Rank in this category: #$PopularityInCategory</h3><% end_if %>
</section>

<section class="consultation-content">
	$Content
</section>

<section class="consultation-reports">
	<% loop Reports %>
		$Me.generate()
	<% end_loop %>
</section>

<section class="consultation-comments">
	<% loop Submissions %>
		<% include ConsultationComment %>
	<% end_loop %>
</section>
