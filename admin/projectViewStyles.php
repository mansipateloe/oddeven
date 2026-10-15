<?php
function oecrm_work_status_label($status)
{
    return ['inprogress' => 'In Progress', 'cancel' => 'Cancelled'][$status]
        ?? ucwords(str_replace('_', ' ', (string) $status));
}
?>
<style>
.admin-portal .work-management-page { --work-gap: 18px; }
.admin-portal .work-management-page .work-page-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 18px; margin-bottom: 22px; }
.admin-portal .work-management-page .work-page-heading h2 { margin: 0 0 6px; color: #202124; font-size: 24px; font-weight: 800; line-height: 1.3; overflow-wrap: anywhere; }
.admin-portal .work-management-page .work-page-heading p { margin: 0; color: #667085; font-size: 14px; line-height: 1.6; }
.admin-portal .work-management-page .work-back-link { display: inline-block; margin-bottom: 12px; color: #b45309; }
.admin-portal .work-management-page .resource-actions { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px; padding: 0; }
.admin-portal .work-management-page .work-page-heading .btn { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap; }
.admin-portal .work-management-page .panel { margin-bottom: 18px !important; }
.admin-portal .work-management-page .foundation-heading { flex-wrap: wrap; }
.admin-portal .work-management-page .work-panel-count { font-size: 13px; color: #667085; font-weight: 500; }
.admin-portal .work-management-page .resource-summary { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 18px; margin-bottom: 18px; }
.admin-portal .work-management-page .resource-summary > div { min-width: 0; padding: 18px; }
.admin-portal .work-management-page .resource-summary small { font-size: 13px; line-height: 1.5; }
.admin-portal .work-management-page .project-board-head { grid-template-columns: repeat(3,minmax(0,1fr)); gap: 18px; margin-bottom: 18px; }
.admin-portal .work-management-page .project-board-head > div { padding: 18px; min-width: 0; }
.admin-portal .work-management-page .project-board-head small { font-size: 12px; font-weight: 600; }
.admin-portal .work-management-page .project-board-head strong { font-size: 17px; line-height: 1.5; overflow-wrap: anywhere; }
.admin-portal .work-management-page .project-board-head em { font-size: 12px; line-height: 1.5; margin-top: 5px; font-style: normal; color: #667085; }
.admin-portal .work-management-page .project-board-grid { grid-template-columns: minmax(0,2.5fr) minmax(260px,1fr); gap: 18px; align-items: start; }
.admin-portal .work-management-page .project-board-grid > * { min-width: 0; }
.admin-portal .work-management-page .project-side-list > div { align-items: flex-start; padding: 12px 0; line-height: 1.6; }
.admin-portal .work-management-page .project-side-list span { min-width: 0; overflow-wrap: anywhere; }
.admin-portal .work-management-page .project-side-list small { font-size: 12px; }
.admin-portal .work-management-page .project-side-list .deadline-history-item { display: block; }
.admin-portal .work-management-page .work-table-scroll { width: 100%; overflow-x: auto; }
.admin-portal .work-management-page .foundation-table { width: 100% !important; }
.admin-portal .work-management-page .foundation-table > thead > tr > th { padding: 13px 12px; white-space: nowrap; }
.admin-portal .work-management-page .foundation-table > tbody > tr > td { padding: 14px 12px; font-size: 13px; line-height: 1.6; }
.admin-portal .work-management-page .foundation-table strong { display: block; color: #344054; font-weight: 600; overflow-wrap: anywhere; }
.admin-portal .work-management-page .foundation-table small { display: block; font-size: 12px; line-height: 1.5; color: #667085; margin-top: 4px; }
.admin-portal .work-management-page .foundation-table td:first-child { min-width: 160px; max-width: 360px; overflow-wrap: anywhere; }
.admin-portal .work-management-page .project-progress { width: 100px; margin: 4px 0 6px; }
.admin-portal .work-management-page .work-status,
.admin-portal .work-management-page .work-priority { display: inline-flex; align-items: center; padding: 4px 9px; border-radius: 6px; font-size: 12px; font-weight: 600; line-height: 1.5; white-space: nowrap; background: #f2f4f7; color: #475467; }
.admin-portal .work-management-page .status-open,
.admin-portal .work-management-page .status-pending,
.admin-portal .work-management-page .status-on_hold { background: #fff4dc; color: #8a5700; }
.admin-portal .work-management-page .status-inprogress,
.admin-portal .work-management-page .status-in_progress { background: #eaf2ff; color: #2459a6; }
.admin-portal .work-management-page .status-in_review,
.admin-portal .work-management-page .status-to_be_tested,
.admin-portal .work-management-page .status-staging_server { background: #f2edff; color: #6941a5; }
.admin-portal .work-management-page .status-completed,
.admin-portal .work-management-page .status-closed,
.admin-portal .work-management-page .status-production { background: #e8f7ef; color: #207448; }
.admin-portal .work-management-page .status-cancel,
.admin-portal .work-management-page .status-cancelled { background: #fff0f0; color: #b42332; }
.admin-portal .work-management-page .work-priority.priority-low { background: #e8f7ef; color: #207448; }
.admin-portal .work-management-page .work-priority.priority-medium { background: #fff4dc; color: #8a5700; }
.admin-portal .work-management-page .work-priority.priority-high,
.admin-portal .work-management-page .work-priority.priority-critical { background: #fff0f0; color: #b42332; }
.admin-portal .work-management-page .work-row-actions { display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; }
.admin-portal .work-management-page .work-row-actions form { display: inline-flex; margin: 0; }
.admin-portal .work-management-page .table td:last-child .work-row-actions a,
.admin-portal .work-management-page .table td:last-child .work-row-actions button { display: inline-flex !important; align-items: center; justify-content: center; flex: 0 0 32px; width: 32px !important; height: 32px !important; margin: 0 !important; padding: 0 !important; }
.admin-portal .work-management-page .task-search-field { width: 100%; max-width: 480px; margin-bottom: 18px; }
.admin-portal .work-management-page .task-search-field label,
.admin-portal .work-management-page .task-new-project label { display: block; margin-bottom: 6px; font-size: 13px; color: #344054; }
.admin-portal .work-management-page .task-search-field input { width: 100%; height: 40px; padding: 8px 12px; font-size: 14px; }
.admin-portal .work-management-page .task-search-field small,
.admin-portal .work-management-page .task-company-context { color: #667085; font-size: 13px; line-height: 1.6; }
.admin-portal .work-management-page .task-search-field small { display: block; margin-top: 6px; }
.admin-portal .work-management-page .task-new-project select { width: 240px; max-width: 100%; height: 38px; }
.admin-portal .work-management-page .task-company-context { margin-bottom: 16px; }
.admin-portal .work-management-page .task-list-table td:nth-child(2) { min-width: 200px; max-width: 400px; overflow-wrap: anywhere; }
.admin-portal .work-management-page .dataTables_wrapper .dataTables_filter input { width: 220px; max-width: 100%; }
.admin-portal .work-management-page .dataTables_wrapper .row { margin-top: 0; margin-bottom: 10px; }
#adminTaskDetailModal .detail-grid strong { overflow-wrap: anywhere; font-size: 14px; line-height: 1.6; }
#adminTaskDetailDescription { white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.7; }
#adminTaskDetailAttachments { display: flex; flex-wrap: wrap; gap: 8px; }
@media (max-width: 1199px) {
    .admin-portal .work-management-page .project-board-grid { grid-template-columns: minmax(0,1fr); }
}
@media (max-width: 900px) {
    .admin-portal .work-management-page .work-page-heading { flex-wrap: wrap; align-items: flex-start; }
    .admin-portal .work-management-page .resource-summary,
    .admin-portal .work-management-page .project-board-head { grid-template-columns: repeat(2,minmax(0,1fr)); }
}
@media (max-width: 600px) {
    .admin-portal .work-management-page .work-page-heading > .resource-actions { width: 100%; }
    .admin-portal .work-management-page .task-new-project,
    .admin-portal .work-management-page .task-new-project select { width: 100%; }
    .admin-portal .work-management-page .resource-summary,
    .admin-portal .work-management-page .project-board-head { grid-template-columns: minmax(0,1fr); }
    .admin-portal .work-management-page .dataTables_filter { text-align: left; margin-top: 10px; }
}
</style>