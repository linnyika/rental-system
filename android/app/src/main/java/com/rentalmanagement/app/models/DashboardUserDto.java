package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class DashboardUserDto {

    @SerializedName("id")
    private long id;

    @SerializedName("name")
    private String name;

    @SerializedName("role")
    private String role;

    @SerializedName("contact")
    private String contact;

    @SerializedName("created_at")
    private String createdAt;

    public long getId() { return id; }
    public String getName() { return name; }
    public String getRole() { return role; }
    public String getContact() { return contact; }
    public String getCreatedAt() { return createdAt; }
}
