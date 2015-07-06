<h1>$Title</h1>
<p class="intro">$Introduction</p>

<% if Categories %>
	<h2>Categories</h2>
	<ul>
		<% loop Categories %>
			<li><a href="$Link">$MenuTitle</a></li>
		<% end_loop %>
	</ul>
<% end_if %>

<% if Consultations %>
	<h2>Consultations</h2>
	<ul>
		<% loop Consultations %>
			<li><a href="$Link">$MenuTitle</a></li>
		<% end_loop %>
	</ul>
<% end_if %>