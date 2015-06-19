<% require themedCSS('consultations') %>

<% include EngagementCircle Offset=$EngagementOffset.Offset, Rotate=$EngagementOffset.Rotate %>

<div class="icon">
	<% if $SVGIcon %>$SVGIcon.RAW<% else %>$Parent.SVGIcon.Raw<% end_if %>
</div>

<h1>$Title</h1>

<p><strong><sup>#</sup>{$Ranking}</strong> Most talked about consultation (out of <a href="$Parent.Link">$AllConsultations.Count consultations</a>)</p>

<% if Facts	%>
	<div class="facts_box">
		<h3>Facts &amp; Figures</h3>

		<ul class="list_semantic">
			<% loop Facts %>
				<li class="$EvenOdd <% if Wide %>fact_wide<% end_if %>"><p><strong>$Figure</strong>$Content</p></li>
			<% end_loop %>
		</ul>
	</div>
<% end_if %>

$Content

<h2>Tell us what you think?</h2>
$Form

<% if Submissions %>
	<h2>What you think</h2>

	<% include Idea_Stats %>
	<% include Graph_Benefit %>
	<% include Graph_Priority %>

	<ul>
		<% loop Submissions.Filter('PromotedSummary', 1).Sort('Created', 'DESC').Limit(10) %>
			<li><% include Comment %></li>
		<% end_loop %>
	</ul>

	<p><a href="$FeedbackPage.Link(comments)?ConsultationID=$ID" class="">See all comments</a></p>

	<p><a href="$FeedbackPage.Link(map)?ConsultationID=$ID" class="">View feedback on a map</a></p>
<% end_if %>