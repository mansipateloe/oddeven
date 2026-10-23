<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Account Number</th>
                                <th>IFSC Code</th>
                                <th class="text-right">Opening Balance</th>
                                <th class="text-right">Current Balance</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bankRows && mysqli_num_rows($bankRows) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($bankRows)): ?>
                                    <tr>
                                        <td><?php echo oecrm_h($row["bank_name"]); ?></td>
                                        <td><?php echo oecrm_h($row["account_number"]); ?></td>
                                        <td><?php echo oecrm_h($row["ifsc_code"]); ?></td>
                                        <td class="text-right"><?php echo number_format((float) $row["opening_balance"], 2); ?></td>
                                        <td class="text-right"><?php echo number_format((float) $row["current_balance"], 2); ?></td>
                                        <td class="center text-center">
                                            <?php if (oecrm_bank_details_can_manage($conn, "edit")): ?>
                                                <a class="oecrm-action-btn" href="addBankDetails.php?edit=<?php echo (int) $row["bank_id"]; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if (oecrm_bank_details_can_manage($conn, "delete")): ?>
                                                <form method="post" action="addBankDetails.php" style="display:inline;" data-confirm="Are you sure you want to delete this bank record?">
                                                    <?php echo oecrm_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_bank_details">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row["bank_id"]; ?>">
                                                    <button type="submit" class="btn btn-link oecrm-action-btn oecrm-action-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No bank records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
