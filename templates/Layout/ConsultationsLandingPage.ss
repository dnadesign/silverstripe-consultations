<h1>$Title</h1>
<section class="consultation-landing-content">
	$Content
</section>

<section class="consultation-landing-categories">
	<% if Categories %>
		<h2>Categories</h2>
		<ul>
			<% loop Categories %>
				<li><a href="$Link">$MenuTitle</a></li>
			<% end_loop %>
		</ul>
	<% end_if %>
</section>

<section class="consultation-landing-consultations">
	<% if Consultations %>
		<h2>Consultations</h2>
		<ul>
			<% loop Consultations %>
				<li><a href="$Link">$MenuTitle</a></li>
			<% end_loop %>
		</ul>
	<% end_if %>
</section>