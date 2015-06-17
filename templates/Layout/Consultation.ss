<% include Header %>

<div class="banner_intro">
	<div class="wrapper">
		<div class="banner_breadcrumbs">
			<div class="banner_intro_icon">
				<% include EngagementCircle Offset=$EngagementOffset.Offset, Rotate=$EngagementOffset.Rotate %>

				<div class="icon">
					<% if $SVGIcon %>$SVGIcon.RAW<% else %>$Parent.SVGIcon.Raw<% end_if %>
				</div>
			</div>
		</div>

		<h1>$Title</h1>

		<% if Introduction %><p class="intro">$Introduction</p><% end_if %>

		<div class="stat_up stat_up__single">
			<p><strong><sup>#</sup>{$Ranking}</strong> Most talked about idea (out of <a href="$BigIdeasPage.Link">$TotalNumberOfIdeas ideas</a>)</p>
		</div>

		<% include ShareThis %>
	</div>
</div>

<div class="wrapper">
	<div class="col_right col_right__wide col__tightbottom">
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
	</div>

	<div class="col_left col_left__wide col_left__padd">
		<div class="content_components">
			$WidgetArea
			$Form
		</div>
	
	</div>
</div>
<!--
<% include FeedbackButton %>
-->
<% if Submissions %>
<div class="wrapper_yellow graphs">	
	<div class="wrapper wrapper_gray">
		<h2 class="feature push">What you think</h2>

		<div class="stat_up stat_up__four stat_up__dark stat_up__tight">
			<% include Idea_Stats %>
		</div>

		<div class="summary_graphs summary_graphs__dark halves ">
			<div class="halve_left">
				<% include Graph_Benefit %>
			</div>

			<div class="halve_right">
				<% include Graph_Priority %>
			</div>
		</div>
	</div>
</div>

<div class="wrapper_yellow">
	<div class="wrapper">
		<div class="comments_grid__container">
			<div class="comments_grid comments_grid__left">
				<ul>
					<% loop Submissions.Filter('PromotedSummary', 1).Sort('DateSubmitted', 'DESC').Limit(8) %>	
						<% if Odd %>
							<li><% include Comment %></li>
						<% end_if %>
					<% end_loop %>
				</ul>
			</div>

			<div class="comments_grid comments_grid__right">
				<ul>
					<% loop Submissions.Filter('PromotedSummary', 1).Sort('DateSubmitted', 'DESC').Limit(8) %>	
						<% if Even %>
							<li><% include Comment %></li>
						<% end_if %>
					<% end_loop %>
				</ul>
			</div>
		</div>

		<div class="halves">
			<div class="halve_left">
				<p class="button_right"><a href="$FeedbackPage.Link(comments)?IdeaID=$ID" class="button button__dark button__small">See all comments</a></p>
			</div>

			<div class="halve_right">
				<p class="button_left"><a href="$FeedbackPage.Link(map)?IdeaID=$ID" class="button  button__dark button__small">View feedback on a map</a></p>
			</div>
		</div>
	</div>
</div>
<% end_if %>