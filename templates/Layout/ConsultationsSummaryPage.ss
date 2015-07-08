<h1>$Title</h1>

$ConsultationSelectorForm

<% with ConsultationSummary %>
<div class="summary">
	<table>
		<thead>
			<tr>
				<td>Participation</td>
				<td><% if isDetail %>Most Popular <% else %>Consultation<% end_if %></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>$Participation</td>
				<% if isDetail %>
					<td><a href="$Consultation.Link()">$Consultation.Title</a></td>					
				<% else %>
					<td><a href="$ConsultationSummaryLink">$Consultation.Title</a></td>
				<% end_if %> 
			</tr>
			<% if Consultation.Reports %>
			<tr>
				<td colspan="2">
				<% loop Consultation.Reports %>
						$Me.generate()
					<% end_loop %>
				</td>
			</tr>
			<% end_if %>
		</tboby> 
	</table>
</div>
<% end_with %>

<% loop Comments %>
	<hr/>
	<% include ConsultationComment %>
<% end_loop %>
