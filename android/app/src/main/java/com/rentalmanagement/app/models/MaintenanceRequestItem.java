package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class MaintenanceRequestItem {

    @SerializedName("id")
    private long id;

    @SerializedName("tenant_id")
    private Long tenantId;

    @SerializedName("unit_id")
    private Long unitId;

    @SerializedName("description")
    private String description;

    @SerializedName("status")
    private String status;

    @SerializedName("is_major")
    private Boolean major;

    @SerializedName("created_at")
    private String createdAt;

    @SerializedName("unit")
    private UnitItem unit;

    @SerializedName("task")
    private TaskSummary task;

    public long getId() {
        return id;
    }

    public Long getTenantId() {
        return tenantId;
    }

    public Long getUnitId() {
        return unitId;
    }

    public String getDescription() {
        return description;
    }

    public String getStatus() {
        return status;
    }

    public Boolean getMajor() {
        return major;
    }

    public String getCreatedAt() {
        return createdAt;
    }

    public UnitItem getUnit() {
        return unit;
    }

    public TaskSummary getTask() {
        return task;
    }
}
