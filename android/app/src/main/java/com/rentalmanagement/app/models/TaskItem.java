package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class TaskItem {

    @SerializedName("id")
    private long id;

    @SerializedName("maintenance_request_id")
    private Long maintenanceRequestId;

    @SerializedName("caretaker_id")
    private Long caretakerId;

    @SerializedName("status")
    private String status;

    @SerializedName("tenant_confirmed")
    private Boolean tenantConfirmed;

    @SerializedName("completed_at")
    private String completedAt;

    @SerializedName("created_at")
    private String createdAt;

    @SerializedName("request")
    private MaintenanceRequestItem request;

    public long getId() {
        return id;
    }

    public Long getMaintenanceRequestId() {
        return maintenanceRequestId;
    }

    public Long getCaretakerId() {
        return caretakerId;
    }

    public String getStatus() {
        return status;
    }

    public Boolean getTenantConfirmed() {
        return tenantConfirmed;
    }

    public String getCompletedAt() {
        return completedAt;
    }

    public String getCreatedAt() {
        return createdAt;
    }

    public MaintenanceRequestItem getRequest() {
        return request;
    }
}
