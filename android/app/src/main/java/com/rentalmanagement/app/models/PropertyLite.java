package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class PropertyLite {

    @SerializedName("id")
    private Integer id;

    @SerializedName("name")
    private String name;

    public Integer getId() {
        return id;
    }

    public String getName() {
        return name;
    }
}
