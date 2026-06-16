<?php
$active_menu = 'lead';
$active_submenu = 'view_lead';
include 'header.php';
$companyId = oecrm_current_company_id($conn);
oecrm_require_permission($conn, 'clients', 'view');
$flash = $_SESSION['lead_flash'] ?? '';
unset($_SESSION['lead_flash']);
$stmt = mysqli_prepare($conn, 'SELECT l.*,s.name source_name FROM leads l LEFT JOIN lead_source_tbl s ON s.id=l.lead_source WHERE l.company_id=? AND l.is_active=1 ORDER BY l.lead_id DESC');
mysqli_stmt_bind_param($stmt, 'i', $companyId);
mysqli_stmt_execute($stmt);
$leads = mysqli_stmt_get_result($stmt);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if($flash):?><div class="alert alert-success"><?php echo oecrm_h($flash);?></div><?php endif;?>
    <div class="lead-page-header">
        <div><h2>Lead Pipeline</h2><small>Manage active prospects and follow-ups</small></div>
        <a href="add_lead.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Lead</a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Active Leads</span><small><?php echo (int)mysqli_num_rows($leads);?> records</small></div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead><tr><th>Date</th><th>Lead</th><th>Contact</th><th>Source</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if(mysqli_num_rows($leads)===0):?><tr><td colspan="6" class="empty-cell">No active leads found.</td></tr><?php endif;?>
                <?php while($lead=mysqli_fetch_assoc($leads)):?>
                    <tr>
                        <td><?php echo $lead['lead_date']?date('d M Y',strtotime($lead['lead_date'])):'-';?></td>
                        <td class="lead-name-cell"><strong><?php echo oecrm_h($lead['executive_name']);?></strong><small><?php echo oecrm_h($lead['company_name']?:$lead['nick_name']);?></small></td>
                        <td class="lead-contact-cell"><strong><?php echo oecrm_h($lead['contact_person']?:'-');?></strong><small><?php echo oecrm_h(trim(($lead['email']?:'').' '.($lead['mobile_no1']?:'')));?></small></td>
                        <td><?php echo oecrm_h($lead['source_name']?:'-');?></td>
                        <td><span class="lead-status"><?php echo oecrm_h(str_replace('_',' ',$lead['status']));?></span></td>
                        <td><a class="icon-action" href="lead_details.php?leadId=<?php echo (int)$lead['lead_id'];?>" title="View"><i class="fa fa-eye"></i></a> <a class="icon-action" href="lead_edit.php?edit=<?php echo (int)$lead['lead_id'];?>" title="Edit"><i class="fa fa-pencil"></i></a> <a class="icon-action danger" href="delete_lead.php?delete=<?php echo (int)$lead['lead_id'];?>" data-confirm="Archive this lead?" title="Archive"><i class="fa fa-archive"></i></a></td>
                    </tr>
                <?php endwhile; mysqli_stmt_close($stmt);?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
