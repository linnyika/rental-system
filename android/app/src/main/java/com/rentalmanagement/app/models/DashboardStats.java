package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class DashboardStats {

    @SerializedName(value = "total_users", alternate = {"totalUsers"})
    private Integer totalUsers;

    @SerializedName("total_properties")
    private Integer totalProperties;

    @SerializedName("total_landlords")
    private Integer totalLandlords;

    @SerializedName("total_tenants")
    private Integer totalTenants;

    @SerializedName("total_caretakers")
    private Integer totalCaretakers;

    @SerializedName(value = "total_units", alternate = {"totalUnits"})
    private Integer totalUnits;

    @SerializedName(value = "total_occupied_units", alternate = {"occupied_units", "occupiedUnits"})
    private Integer occupiedUnits;

    @SerializedName(value = "total_vacant_units", alternate = {"vacant_units", "vacantUnits"})
    private Integer vacantUnits;

    @SerializedName("pending_maintenance_requests")
    private Integer pendingMaintenanceRequests;

    @SerializedName(value = "total_active_leases", alternate = {"active_leases", "activeLeases"})
    private Integer totalActiveLeases;

    @SerializedName(value = "total_rent_collected", alternate = {"rent_collected", "totalRentCollected"})
    private Double totalRentCollected;

    @SerializedName(
        value = "total_pending_payments",
        alternate = {"totalPendingPayments"}
    )
    private Integer totalPendingPayments;
    @SerializedName("assigned_tasks")
    private Integer assignedTasks;

    @SerializedName("tasks_in_progress")
    private Integer tasksInProgress;

    @SerializedName("completed_tasks")
    private Integer completedTasks;

    @SerializedName("activity_logs")
    private Integer activityLogs;

    @SerializedName("total_paid")
    private Double totalPaid;

    @SerializedName("pending_payments")
    private Integer pendingPayments;

    @SerializedName("verified_payments")
    private Integer verifiedPayments;

    @SerializedName(value = "payment_count", alternate = {"paymentCount"})
    private Integer paymentCount;

    public Integer getTotalUsers() {
        return totalUsers;
    }

    public Integer getTotalProperties() {
        return totalProperties;
    }

    public Integer getTotalLandlords() {
        return totalLandlords;
    }

    public Integer getTotalTenants() {
        return totalTenants;
    }

    public Integer getTotalCaretakers() {
        return totalCaretakers;
    }

    public Integer getTotalUnits() {
        return totalUnits;
    }

    public Integer getOccupiedUnits() {
        return occupiedUnits;
    }

    public Integer getVacantUnits() {
        return vacantUnits;
    }

    public Integer getPendingMaintenanceRequests() {
        return pendingMaintenanceRequests;
    }

    public Integer getTotalActiveLeases() {
        return totalActiveLeases;
    }

    public Double getTotalRentCollected() {
        return totalRentCollected;
    }

    public Integer getTotalPendingPayments() {
        return totalPendingPayments;
    }

    public Integer getAssignedTasks() {
        return assignedTasks;
    }

    public Integer getTasksInProgress() {
        return tasksInProgress;
    }

    public Integer getCompletedTasks() {
        return completedTasks;
    }

    public Integer getActivityLogs() {
        return activityLogs;
    }

    public Double getTotalPaid() {
        return totalPaid;
    }

    public Integer getPendingPayments() {
        return pendingPayments;
    }

    public Integer getVerifiedPayments() {
        return verifiedPayments;
    }

    public Integer getPaymentCount() {
        return paymentCount;
    }
}
