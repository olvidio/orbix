<style>
/* Slick.Editors.Text, Slick.Editors.Date */
input.editor-text {
  width: 100%;
  height: 100%;
  border: 0;
  margin: 0;
  background: transparent;
  outline: 0;
  padding: 0;
  size:10px;

}
.cell-title {
  font-weight: bold;
}

.cell-effort-driven {
  text-align: center;
}

.cell-selection {
  border-right-color: silver;
  border-right-style: solid;
  background: #f5f5f5;
  color: gray;
  text-align: right;
  font-size: 10px;
}

.selected {
  background-color: #FBB; /* show default selected row background */
}
.slick-row.selected .cell-selection {
  background-color: green; /* show default selected row background */
}

/* con doble click */
.active-row {
     background-color: #FFB;
}
</style>