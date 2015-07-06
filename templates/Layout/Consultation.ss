<h1>$Title</h1>

$Content

<div class="clearfix"></div>

<% if Submissions %>
<hr/>
	<ul>
		<% loop Submissions.Limit(10) %>
			<li><% include ConsultationComment %></li>
		<% end_loop %>
	</ul>

<% end_if %>