package bryntum.gantt.projectreader;

import java.util.Iterator;
import java.util.Locale;
import java.util.Properties;

import org.json.JSONArray;
import org.json.JSONObject;

import net.sf.mpxj.Column;
import net.sf.mpxj.FieldType;
import net.sf.mpxj.ProjectFile;
import net.sf.mpxj.Table;

public class ColumnsJSONBuilder implements JSONBuilder<JSONArray> {

    Properties properties;

    public ColumnsJSONBuilder(Properties properties) {
        this.properties = properties;
    }

    public JSONArray buildJSON(ProjectFile projectFile) {
        JSONArray result = new JSONArray();

        FieldType fieldType;
        String fieldTypeName;
        JSONObject columnJSON;

        Iterator<Table> i = projectFile.getTables().iterator();

        // extract columns
        if (i.hasNext()) {
            Table table = i.next();

            for (Column column : table.getColumns()) {
                fieldType = column.getFieldType();

                // skip column if we don't know its type
                if (fieldType != null) {
                    // get the column type name in US locale
                    fieldTypeName = fieldType.getName(Locale.US);

                    // get column options if any
                    String options = properties.getProperty("columnOptions." + fieldTypeName);
                    String columnXType = properties.getProperty("columnXType." + fieldTypeName);
                    columnJSON = options != null ? new JSONObject(options) : new JSONObject();
                    // skip unknown columns
                    if (options == null && columnXType == null){
                        if(fieldType.toString().contains("Text")){
                            columnJSON.put("field",fieldType.toString());
                            columnJSON.put("label",column.getTitle());
                            columnJSON.put("personalized",true);
                            result.put(columnJSON);
                        }
                        continue;
                    }


                    if (columnXType != null) {
                        columnJSON.put(properties.getProperty("column.XTYPE"), columnXType);
                    }

                    result.put(columnJSON);
                }
            }
        }

        return result;
    }

}
