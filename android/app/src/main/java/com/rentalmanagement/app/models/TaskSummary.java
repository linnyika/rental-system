package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class TaskSummary {

    @SerializedName("id")
    private long id;

    @SerializedName("status")
    private String status;

    @SerializedName("tenant_confirmed")
    private Boolean tenantConfirmed;

    @SerializedName("completed_at")
    private String completedAt;

    public long getId() {
        return id;
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
}
