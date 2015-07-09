<h1>$Breadcrumbs</h1>

<section class="consultation-category-content">
	$Content
</section>

<section class="consultation-category-consultations">
	<h2>Consultations</h2>
	<ul>
		<% loop Consultations %>
			<li><a href="$Link">$MenuTitle</a></li>
		<% end_loop %>
	</ul>
</section>
