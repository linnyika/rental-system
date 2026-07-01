package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class PropertiesResponse {

    @SerializedName("properties")
    private List<PropertyItem> properties;

    public List<PropertyItem> getProperties() {
        return properties;
    }
}
